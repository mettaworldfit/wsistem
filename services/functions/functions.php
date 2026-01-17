<?php

/**
 * Maneja una solicitud server-side de DataTables, aplicando búsqueda, orden y paginación.
 *
 * @param mysqli $db Conexión activa a la base de datos MySQLi.
 * @param array $params Arreglo asociativo con los siguientes elementos:
 * 
 * - 'columns' (array): Lista de nombres de columnas permitidas para ordenamiento.
 * - 'searchable' (array): Lista de columnas sobre las cuales se puede aplicar la búsqueda global.
 * - 'base_table' (string): Nombre de la tabla principal para conteo general sin filtros ni joins.
 * - 'table_with_joins' (string): Tabla principal junto con cualquier JOIN necesario para consultas con filtros.
 * - 'select' (string): Sentencia SELECT (sin el FROM), es decir, qué columnas seleccionar.
 * - 'base_condition' (string): Cláusula WHERE de la consulta (por ejemplo, filtro por producto_id).
 * - 'table_rows' (callable): Callback que recibe una fila y devuelve un arreglo con el formato requerido por DataTables.
 *
 * @return void Imprime un JSON con los datos esperados por DataTables:
 *              - draw: número de iteración enviado por DataTables.
 *              - recordsTotal: total de registros sin filtros.
 *              - recordsFiltered: total de registros luego de aplicar búsqueda.
 *              - data: arreglo con los datos formateados.
 */
function handleDataTableRequest(mysqli $db, array $params)
{
    // Parámetros base enviados por DataTables
    $draw = intval($_POST['draw'] ?? 0);
    $start = intval($_POST['start'] ?? 0);
    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
    if ($length <= 0) {
        $length = 10; // Evitar valores negativos o inválidos
    }

    // Configuración de búsqueda y orden
    $searchValue = $_POST['search']['value'] ?? '';
    $columns = $params['columns'];
    $orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
    $orderColumn = $columns[$orderColumnIndex] ?? $columns[0];
    $orderDir = ($_POST['order'][0]['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

    // Condición base (filtro general)
    $baseCondition = trim($params['base_condition'] ?? '1=1');
    $where = $baseCondition;

    // Aplicar búsqueda global si hay texto
    if (!empty($searchValue) && !empty($params['searchable'])) {
        $searchEscaped = $db->real_escape_string($searchValue);

        // Crear condiciones LIKE dinámicas para las columnas buscables
        $searchConditions = array_map(function ($col) use ($searchEscaped) {
            return "$col LIKE '%$searchEscaped%'";
        }, $params['searchable']);

        // Agregar al WHERE con OR entre columnas
        $where .= " AND (" . implode(' OR ', $searchConditions) . ")";
    }

    // ===============================================================
    // Detección inteligente del tipo de tabla para el conteo total
    // ===============================================================
    // Si el base_condition contiene alias (ej. "p.producto_id"), usamos los JOINs
    // Si no, contamos directamente desde la tabla base
    $tableForCount = (str_contains($baseCondition, '.') && $baseCondition !== '1=1')
        ? $params['table_with_joins']
        : $params['base_table'];

    // Conteo total de registros (sin búsqueda, pero respetando el filtro si lo hay)
    $totalQuery = "SELECT COUNT(*) AS total FROM $tableForCount WHERE $baseCondition";
    $totalResult = $db->query($totalQuery);
    $totalRecords = $totalResult->fetch_assoc()['total'] ?? 0;

    // Conteo de registros luego de aplicar búsqueda
    $filteredQuery = "SELECT COUNT(*) AS total FROM {$params['table_with_joins']} WHERE $where";
    $filteredResult = $db->query($filteredQuery);
    $filteredRecords = $filteredResult->fetch_assoc()['total'] ?? 0;

    // Consulta principal con orden y paginación
    $query = "{$params['select']} 
              FROM {$params['table_with_joins']} 
              WHERE $where 
              ORDER BY $orderColumn $orderDir";

    if ($length > 0) {
        $query .= " LIMIT $start, $length";
    }

    // Ejecución de la consulta y formateo de resultados
    $result = $db->query($query);
    $data = [];

    while ($row = $result->fetch_assoc()) {
        // Callback definido por el usuario para dar formato a cada fila
        $data[] = call_user_func($params['table_rows'], $row);
    }

    // Respuesta final compatible con DataTables
    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => $totalRecords,      // Total general (filtrado por producto si aplica)
        "recordsFiltered" => $filteredRecords, // Total luego de búsqueda
        "data" => $data                       // Datos finales
    ]);
}


/**
 * Abrevia un texto si supera la longitud máxima permitida.
 *
 * @param string $text Texto original.
 * @param int $maxLength Longitud máxima antes de cortar.
 * @return string Texto abreviado con "..." si es necesario.
 */
function shortenText($text, $maxLength = 20)
{
    $text = trim($text);
    if (strlen($text) <= $maxLength) {
        return $text;
    }

    return mb_substr($text, 0, $maxLength - 3) . '...';
}


/**
 * Formatea numeros telefonicos.
 *
 * @param string $numero numero telefonico a formatear
 */

function formatTel(string $numero = ''): string
{
    // Eliminar todo lo que no sea dígito
    // Asegura que $numero no sea null, sino una cadena vacía si lo es
    $numero = preg_replace('/\D/', '', $numero ?? '');

    // Si tiene más de 10 dígitos, separar el código de país
    if (strlen($numero) > 10) {
        $codigoPais = substr($numero, 0, strlen($numero) - 10);
        $numeroLocal = substr($numero, -10);
        return '+' . $codigoPais . ' (' .
            substr($numeroLocal, 0, 3) . ') ' .
            substr($numeroLocal, 3, 3) . '-' .
            substr($numeroLocal, 6);
    } elseif (strlen($numero) === 10) {
        // Sin código de país
        return '(' . substr($numero, 0, 3) . ') ' .
            substr($numero, 3, 3) . '-' .
            substr($numero, 6);
    } else {
        return $numero; // No formatear si tiene menos de 10 dígitos
    }
};

/**
 * Ejecuta un procedimiento almacenado para eliminar un registro por su ID.
 *
 * @param mysqli $db Conexión activa a la base de datos.
 * @param int $id ID del registro que se desea eliminar.
 * @param string $procedureName Nombre del procedimiento almacenado que realizará la eliminación.
 * @return string Resultado de la operación: "ready" si fue exitosa o un mensaje de error.
 */
function handleDeletionAction(mysqli $db, int $id, string $procedureName): string
{
    if (empty($procedureName)) {
        return "Nombre del procedimiento requerido.";
    }

    $query = "CALL $procedureName($id)";
    $result = $db->query($query);

    if (!$result) {
        return "Error al ejecutar el procedimiento: " . $db->error;
    }

    $data = $result->fetch_object();

    // Validar que $data es un objeto y que contiene 'msg'
    if (!$data || !isset($data->msg)) {
        return "Error: Respuesta inesperada del procedimiento $procedureName.";
    }

    if ($data->msg === "ready") {
        return "ready";
    } else {
        return "Error en $procedureName: " . $data->msg;
    }
}


/**
 * Ejecuta un procedimiento almacenado con parámetros dados y devuelve el resultado según el mensaje de salida.
 *
 * @param mysqli $db Conexión activa a la base de datos.
 * @param string $procedure Nombre del procedimiento almacenado.
 * @param array $params Lista de parámetros en orden para el procedimiento.
 * @return string Resultado de la operación: "ready", "duplicate", error SQL, o mensaje personalizado.
 */
function handleProcedureAction(mysqli $db, string $procedure, array $params): string
{
    // Escapar los parámetros, pero asegurándonos de que 'codigo' siempre sea tratado como una cadena
    $escapedParams = array_map(function ($param) use ($db) {
        // Asegurar que 'codigo' siempre sea tratado como cadena de texto
        if (is_numeric($param) && !is_string($param)) {
            // Si es numérico, devolverlo tal cual
            return $param;
        } elseif (is_string($param)) {
            // Si es una cadena, escapar el valor
            return "'" . $db->real_escape_string($param) . "'";
        } elseif (empty($param)) {
            // Si el valor está vacío, devolver NULL
            return "NULL";
        } else {
            // En cualquier otro caso, escaparlo correctamente
            return "'" . $db->real_escape_string($param) . "'";
        }
    }, $params);

    // Crear la consulta con los parámetros escapados
    $query = "CALL $procedure(" . implode(',', $escapedParams) . ")";

    try {
        // Ejecutar la consulta
        if (!$db->multi_query($query)) {
            // Si la consulta falla, lanzar una excepción con el error de MySQL
            throw new mysqli_sql_exception("Error en $procedure: " . $db->error);
        }

        // Recorrer los result sets si hay más de uno
        do {
            if ($result = $db->store_result()) {
                // Procesar el primer conjunto de resultados
                $row = $result->fetch_assoc();
                $result->free();

                // Si la respuesta tiene un campo 'msg', devolverlo
                if (isset($row['msg'])) {
                    return $row['msg'];
                }
            }
        } while ($db->more_results() && $db->next_result());

        // Si no se recibe ningún mensaje, devolver un error genérico
        return "Error: No se recibió respuesta del procedimiento.";

    } catch (mysqli_sql_exception $e) {
        // Capturar la excepción y devolver un mensaje detallado de error
        return "Error: " . $e->getMessage();
    }
}



/**
 * Ejecuta una consulta SQL y devuelve el resultado en formato JSON.
 *
 * - Si hay un solo resultado: retorna un objeto JSON.
 * - Si hay varios resultados: retorna un array de objetos JSON.
 * - Si hay un error SQL: retorna un mensaje de error detallado.
 *
 * @param mysqli $db    Conexión activa a la base de datos.
 * @param string $query Consulta SQL a ejecutar (NO preparada).
 *
 * @return void
 */
function jsonQueryResult(mysqli $db, string $query): void
{
    $result = $db->query($query);

    if (!$result) {
        echo json_encode([
            'error' => true,
            'message' => 'Error en la consulta SQL',
            'sql_error' => $db->error,
            'sql' => $query // útil para depurar
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Si no hay resultados
    if ($result->num_rows === 0) {
        echo json_encode(null, JSON_UNESCAPED_UNICODE);
    }

    // Si hay un solo resultado
    elseif ($result->num_rows === 1) {
        echo json_encode([$result->fetch_assoc()], JSON_UNESCAPED_UNICODE);
    }

    // Si hay múltiples resultados
    else {
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row; // Agrega correctamente cada fila como array
        }
        echo json_encode($rows, JSON_UNESCAPED_UNICODE);
    }

    exit;
}


/**
 * Ejecuta múltiples consultas SQL separadas por punto y coma (;) usando mysqli
 * y devuelve los resultados como un array JSON indexado (por posición).
 *
 * Cada conjunto de resultados se almacena como un array de objetos asociativos,
 * y se devuelve en una estructura tipo: [ [fila1, fila2], [fila1, fila2], ... ]
 *
 * Si una consulta no retorna filas (por ejemplo, un UPDATE), se inserta `null`.
 * Si ocurre un error en la ejecución, se devuelve un JSON con detalles del error.
 *
 * @param mysqli $db     Conexión activa a la base de datos MySQL.
 * @param string $query  Cadena con múltiples consultas separadas por `;`.
 *
 * @return void          La función imprime directamente un JSON y finaliza con `exit`.
 */
function jsonMultiQueryResult(mysqli $db, string $query): void
{
    $data = [];

    if ($db->multi_query($query)) {
        do {
            // Captura el resultado actual
            if ($result = $db->store_result()) {
                $rows = [];
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                }
                $data[] = $rows;
                $result->free();
            } else {
                // Si no hay resultado (por ejemplo, UPDATE), agregar null
                $data[] = null;
            }
        } while ($db->more_results() && $db->next_result());
    } else {
        echo json_encode([
            'error' => true,
            'message' => 'Error al ejecutar múltiples consultas',
            'sql_error' => $db->error,
            'sql' => $query
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}


/**
 * Ejecuta una consulta SQL y devuelve una respuesta en formato JSON
 * indicando el éxito o error de la operación, así como el número de
 * filas afectadas en el caso de las consultas `UPDATE`.
 *
 * @param mysqli $db Conexión activa a la base de datos.
 * @param string $query Consulta SQL a ejecutar.
 * 
 * @return void Devuelve un JSON con el resultado de la operación.
 */
function jsonQueryRowAffected(mysqli $db, string $query): void
{
    // Ejecutar la consulta
    $result = $db->query($query);

    // Verificar si la consulta ha tenido éxito
    if (!$result) {
        echo json_encode([
            'error' => true,
            'message' => 'Error en la consulta SQL',
            'sql_error' => $db->error,
            'sql' => $query // útil para depurar
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Si la consulta es un UPDATE, podemos verificar si se realizó algún cambio
    if ($db->affected_rows > 0) {
        echo json_encode([
            'error' => false,
            'message' => 'Operación exitosa, filas afectadas: ' . $db->affected_rows
        ], JSON_UNESCAPED_UNICODE);
    } else {
        // Si no hubo filas afectadas (por ejemplo, la cantidad no cambió)
        echo json_encode([
            'error' => false,
            'message' => 'No se realizaron cambios en la base de datos.'
        ], JSON_UNESCAPED_UNICODE);
    }

    exit;
}


/**
 * Actualiza la cantidad de un detalle (producto, pieza, o servicio) en la base de datos.
 *
 * @param mysqli $db Conexión a la base de datos.
 * @param int $id ID del detalle a actualizar.
 * @param int $quantity Nueva cantidad a actualizar.
 * @param int $item_id ID del producto, pieza o servicio.
 * @param string $item_type Tipo de ítem: 'producto', 'pieza', 'servicio'.
 * @param string $tabla_detalle Nombre de la tabla de detalles (ej. 'detalle_temporal' o 'detalle_facturas_ventas').
 * @param string $tabla_id Nombre de la columna ID en la tabla de detalles (ej. 'detalle_temporal_id' o 'detalle_venta_id').
 * @return string JSON con el estado de la operación.
 */
function updateDetailQuantity($db, $id, $quantity, $item_id, $item_type, $tabla_detalle, $tabla_id)
{
    // Validación de entrada
    if ($quantity <= 0 || $id <= 0 || $item_id <= 0 || empty($item_type)) {
        return json_encode(['error' => true, 'message' => 'Datos inválidos.']);
    }

    // Si el item es un servicio, solo actualizamos la cantidad sin verificar stock
    if ($item_type === 'servicio') {
        $sql_update = "UPDATE $tabla_detalle SET cantidad = ? WHERE $tabla_id = ?";
        $stmt = $db->prepare($sql_update);
        $stmt->bind_param('ii', $quantity, $id);
        if ($stmt->execute()) {
            return json_encode(['error' => false, 'message' => 'Detalle actualizado con éxito (Servicio).']);
        }
        return json_encode(['error' => true, 'message' => 'Error al actualizar el servicio.']);
    }

    // Si es un producto o pieza, verificamos stock
    $sql_check = "";
    switch ($item_type) {
        case 'producto':
            $sql_check = "SELECT cantidad FROM productos WHERE producto_id = ?";
            break;
        case 'pieza':
            $sql_check = "SELECT cantidad FROM piezas WHERE pieza_id = ?";
            break;
    }

    if ($sql_check) {
        // Verificar stock en producto o pieza
        $stmt_check = $db->prepare($sql_check);
        $stmt_check->bind_param('i', $item_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $current_stock = $row['cantidad'];

            // Obtener la cantidad actual en detalle
            $sql_detalle_check = "SELECT cantidad FROM $tabla_detalle WHERE $tabla_id = ?";
            $stmt_detalle = $db->prepare($sql_detalle_check);
            $stmt_detalle->bind_param('i', $id);
            $stmt_detalle->execute();
            $detalle_result = $stmt_detalle->get_result();

            if ($detalle_result->num_rows > 0) {
                $detalle_row = $detalle_result->fetch_assoc();
                $current_detail_quantity = $detalle_row['cantidad']; // Cantidad en detalle

                // Calcular el stock total disponible
                $total_available = $current_stock + $current_detail_quantity;

                // Verificar si hay suficiente stock
                if ($total_available >= $quantity) {
                    // Actualizar la cantidad en detalle
                    $sql_update = "UPDATE $tabla_detalle SET cantidad = ? WHERE $tabla_id = ?";
                    $stmt_update = $db->prepare($sql_update);
                    $stmt_update->bind_param('ii', $quantity, $id);
                    if ($stmt_update->execute()) {
                        return json_encode(['error' => false, 'message' => 'Detalle modificado con éxito.']);
                    }
                    return json_encode(['error' => true, 'message' => 'Error al actualizar la cantidad en detalle.']);
                } else {
                    return json_encode(['error' => true, 'message' => 'No hay suficiente stock para realizar la actualización.']);
                }
            } else {
                return json_encode(['error' => true, 'message' => 'Error al obtener la cantidad en detalle.']);
            }
        } else {
            return json_encode(['error' => true, 'message' => 'Error al verificar el stock del item.']);
        }
    }

    return json_encode(['error' => true, 'message' => 'Tipo de item no reconocido.']);
}


/**
 * Verifica si el usuario autenticado tiene permiso para ejecutar una acción.
 *
 * Esta función valida:
 * 1. Que el usuario esté autenticado.
 * 2. Que la acción exista dentro del array de permisos.
 * 3. Que el rol del usuario tenga autorización para dicha acción.
 *
 * Si alguna validación falla, se detiene la ejecución y se devuelve
 * una respuesta JSON con el código HTTP correspondiente.
 *
 * @param string $action Acción solicitada 
 * @param array  $permissions Array de permisos por acción y roles permitidos
 *
 * @return void
 */
function check_permission_action($action, $permissions)
{
    // No autenticado
    if (!isset($_SESSION['identity'])) {
        http_response_code(401);
        echo json_encode(['error' => 'No autenticado']);
        exit;
    }

    // Acción no definida en permisos → DENEGADA por seguridad
    if (!isset($permissions[$action])) {
        http_response_code(403);
        echo json_encode(['error' => 'Acción no permitida']);
        exit;
    }

    $rolUsuario = $_SESSION['identity']->nombre_rol;

    // Verifica si el array de permisos está vacío, permitiendo acceso para todos
    if (empty($permissions[$action]) || in_array($rolUsuario, $permissions[$action])) {
        return; // Acción permitida
    } else {
        http_response_code(403);
        echo json_encode(['error' => 'Permiso denegado']);
        exit;
    }
}
