<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();
$db = Database::connect();
$config = Database::getConfig();
$action = $_POST['action'] ?? null;
$user_id = $_SESSION['identity']->usuario_id;

$permissions = [
    'index_facturas_emitidas' => ['administrador'],
    'consultar_ecf' => ['administrador'],
    'subir_certificado' => ['administrador'],
    'datos_contribuyente' => ['administrador'],
    'consultar_steps' => ['administrador'],
    'completar_step' => ['administrador'],
    'volver_al_step' => ['administrador']
];

// Chequear permisos
if (isset($_POST['action'])) {
    check_permission_action($_POST['action'], $permissions);
}

switch ($action) {
    case 'index_facturas_emitidas':

        handleDataTableRequest($db, [
            'columns' => ['id', 'nombre_cliente', 'creado_en', 'monto_total', 'encf', 'estado'],
            'searchable' => ['nombre_cliente', 'encf', 'creado_en'],
            'base_table' => 'facturas_electronicas f',

            'table_with_joins' => 'facturas_electronicas f INNER JOIN usuarios u ON u.usuario_id = f.usuario_id',

            'select' => 'SELECT f.id, f.nombre_cliente, f.creado_en, f.monto_total, f.encf, 
            CONCAT(u.nombre," ",IFNULL(u.apellidos,"")) as nombre, f.estado',

            'table_rows' => function ($row) {

                $encf = '<td><a class="this-ncf ';

                if (strtolower($row['estado']) == "rechazado" || strtolower($row['estado']) == "error") {
                    $encf .= "text-danger";
                } elseif (strtolower($row['estado']) == "aceptado") {
                    $encf .= "text-success";
                } elseif (strtolower($row['estado']) == "en proceso") {
                    $encf .= "text-warning";
                }
                $encf .= '" href="#" data-toggle="modal" data-target="#modalNCF" data-ncf="' . $row['encf'] . '">' . $row['encf'] . "</a></td>";

                // Estado
                $estadoTexto = mb_strtolower(
                    $row['estado'],
                    'UTF-8'
                );

                $class = '';

                switch ($estadoTexto) {
                    case 'rechazado':
                    case 'error':
                        $class = 'text-danger';
                        break;

                    case 'aceptado':
                        $class = 'text-success';
                        break;

                    case 'en proceso':
                        $class = 'text-warning';
                        break;
                }

                $estado = "<td>
                    <p class='{$class}'>
                        {$row['estado']}
                    </p>
                           </td>";

                return [
                    'id' => "<td>" . $row['id'] . "</td>",
                    'nombre_cliente' => "<td>" . $row['nombre_cliente'] . "</td>",
                    'creado_en' => "<td>" . $row['creado_en'] . "</td>",
                    'monto_total' => "<td>" . number_format($row['monto_total'] ?? 0, 2) . "</td>",
                    'encf' => $encf,
                    'vendedor' => "<td>" . $row['nombre'] . "</td>",
                    'estado' => $estado,
                    'acciones' => '<a href="#">Ver detalle</a>'
                ];
            }
        ]);

        break;

    // Consultar datos eNCF
    case 'consultar_ecf':

        $eNCF = $_POST['encf'];

        $sql = "SELECT * FROM facturas_electronicas WHERE encf = '$eNCF'";

        jsonQueryResult($db, $sql);

        break;

    // Subir certificado
    case 'subir_certificado':
        try {

            if (!isset($_FILES['cert'])) {
                throw new Exception('No se recibió archivo');
            }

            $file = $_FILES['cert'];
            $passcert = $_POST['passcert'];

            // Validar errores de subida
            if ($file['error'] !== UPLOAD_ERR_OK) {

                switch ($file['error']) {

                    case UPLOAD_ERR_INI_SIZE:
                        throw new Exception(
                            'El archivo excede upload_max_filesize'
                        );

                    case UPLOAD_ERR_FORM_SIZE:
                        throw new Exception(
                            'El archivo excede el tamaño permitido'
                        );

                    default:
                        throw new Exception(
                            'Error desconocido subiendo archivo'
                        );
                }
            }

            // Carpeta destino
            $uploadDir = __DIR__ . '/../backend/src/certificates/';

            // Crear carpeta si no existe
            if (!is_dir($uploadDir)) {

                if (!mkdir($uploadDir, 0777, true)) {

                    throw new Exception('No se pudo crear la carpeta');
                }
            }

            // Nombre archivo
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fileName = uniqid() . '.' . $extension;

            $destination = $uploadDir . $fileName;

            // Guardar ruta en DB
            $sql = "UPDATE datos_contribuyente SET route_cert = '$fileName', passphrase_cert = '$passcert' WHERE id = 1";
            $result = $db->query($sql);

            if (!$result) {
                throw new Exception('Error guardando ruta en base de datos');
            }

            // Guardar archivo físicamente
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                throw new Exception('Error guardando archivo');
            }

            echo json_encode([
                'success' => true,
                'message' =>
                'Archivo guardado correctamente'
            ]);
        } catch (Exception $e) {

            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        break;

    // Guardar datos del contribuyente
    case 'datos_contribuyente':

        $params = [
            $_POST['rnc'],
            $_POST['name'],
            $_POST['coname'],
            $_POST['activ_econ'],
            $_POST['regime'],
            $_POST['emisore'],
            $_POST['status'],
            $_POST['enviroment'],
            $_POST['noSerial'],
            $_POST['subject'],
            $_POST['issuer'],
            $_POST['validFrom'],
            $_POST['validTo']
        ];

        echo handleProcedureAction($db, 'fe_update_datos_contribuyente', $params);
        break;

    // Consultar Steps de certificacion
    case 'consultar_steps':

        $sql = "SELECT * FROM certificacion_steps WHERE completado = 1";

        jsonQueryResult($db, $sql);
        break;

    // Completar Step
    case 'completar_step':

        $step = $_POST['step'];

        $sql = "INSERT INTO certificacion_steps VALUES (null,$step,1,CURDATE())";
        $result = $db->query($sql);

        // Error SQL
        if (!$result) {
            return json_encode([
                'success' => false,
                'error' => $db->error
            ]);
        }

        echo json_encode([
            'success' => true,
            'insert_id' => $db->insert_id,
            'affected_rows' => $db->affected_rows
        ]);

        break;
        
    case 'volver_al_step':
        $step = $_POST['step'];

        $sql = "DELETE FROM certificacion_steps WHERE step_numero = '$step'";
        $result = $db->query($sql);

        // Error SQL
        if (!$result) {
            return json_encode([
                'success' => false,
                'error' => $db->error
            ]);
        }

        echo json_encode([
            'success' => true,
            'affected_rows' => $db->affected_rows
        ]);
        break;
}
