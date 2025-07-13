<?php

/**
 * Maneja una solicitud server-side de DataTables, aplicando búsqueda, orden y paginación.
 *
 * @param mysqli $db Conexión activa a la base de datos MySQLi.
 * @param array $params Arreglo asociativo con los siguientes elementos:
 * 
 * - 'columns' (array): Lista de nombres de columnas permitidas para ordenamiento.
 * - 'searchable' (array): Lista de columnas sobre las cuales se puede aplicar la búsqueda global.
 * - 'base_table' (string): Nombre de la tabla principal para contar todos los registros sin filtros.
 * - 'table_with_joins' (string): Tabla principal junto con cualquier JOIN necesario para la consulta con filtros.
 * - 'select' (string): Sentencia SELECT (sin el FROM), es decir, qué columnas seleccionar.
 * - 'base_condition' (string): Clausula WHERE de la consulta si existe.
 * - 'table_rows' (callable): Función de callback que recibe una fila de la base de datos y devuelve un arreglo con el formato requerido por DataTables.
 *
 * @return void Imprime un JSON con los datos esperados por DataTables:
 *              - draw: número de iteración enviado por DataTables.
 *              - recordsTotal: total de registros sin filtrar.
 *              - recordsFiltered: total de registros luego de aplicar el filtro.
 *              - data: arreglo de datos formateado.
 */

function handleDataTableRequest(mysqli $db, array $params)
{
    $draw = intval($_POST['draw'] ?? 0);
    $start = intval($_POST['start'] ?? 0);
    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
    if ($length <= 0) {
        $length = 10; // Evitar -1 o valores inválidos
    }

    $searchValue = $_POST['search']['value'] ?? '';
    $columns = $params['columns'];

    $orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
    $orderColumn = $columns[$orderColumnIndex] ?? $columns[0];
    $orderDir = ($_POST['order'][0]['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

    $baseCondition = $params['base_condition'] ?? '1=1';
    $where = "$baseCondition";

    if (!empty($searchValue) && !empty($params['searchable'])) {
        $searchEscaped = $db->real_escape_string($searchValue);
        $searchConditions = array_map(function ($col) use ($searchEscaped) {
            return "$col LIKE '%$searchEscaped%'";
        }, $params['searchable']);

        $where .= " AND (" . implode(' OR ', $searchConditions) . ")";
    }

    $totalResult = $db->query("SELECT COUNT(*) AS total FROM {$params['base_table']}");
    $totalRecords = $totalResult->fetch_assoc()['total'] ?? 0;

    $filteredQuery = "SELECT COUNT(*) AS total FROM {$params['table_with_joins']} WHERE $where";
    $filteredResult = $db->query($filteredQuery);
    $filteredRecords = $filteredResult->fetch_assoc()['total'] ?? 0;

    $query = "{$params['select']} FROM {$params['table_with_joins']} WHERE $where ORDER BY $orderColumn $orderDir";
    if ($length > 0) {
        $query .= " LIMIT $start, $length";
    }

    $result = $db->query($query);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = call_user_func($params['table_rows'], $row);
    }

    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $filteredRecords,
        "data" => $data
    ]);
}

/**
 * Abrevia un texto si supera la longitud máxima permitida.
 *
 * @param string $text Texto original.
 * @param int $maxLength Longitud máxima antes de cortar.
 * @return string Texto abreviado con "..." si es necesario.
 */
function shortenText($text, $maxLength = 20) {
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
    $escapedParams = array_map(function ($param) use ($db) {
        if (is_numeric($param)) {
            return $param;
        } elseif (!empty($param)) {
            return "'" . $db->real_escape_string($param) . "'";
        } else {
            return "NULL"; // evita pasar ''
        }
    }, $params);


    $query = "CALL $procedure(" . implode(',', $escapedParams) . ")";

    if (!$db->multi_query($query)) {
        return "Error en $procedure: " . $db->error;
    }

    // Recorremos los result sets
    do {
        if ($result = $db->store_result()) {
            $row = $result->fetch_assoc();
            $result->free();

            if (isset($row['msg'])) {
                return $row['msg'];
            }
        }
    } while ($db->more_results() && $db->next_result());

    return "Error: No se recibió respuesta del procedimiento.";
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
