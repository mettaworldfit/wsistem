<?php

require_once '../config/db.php';
require_once '../help.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();
$db = Database::connect();
$config = Database::getConfig();
$user_id = $_SESSION['identity']->usuario_id;
$action = $_POST['action'] ?? null;

require_once __DIR__ . '/../vendor/autoload.php';

$permissions = [
  "fecha_actual" => [],
  "configuracion_de_impresion" => [],

  "index_bonos" => ['administrador'],
  "configuracion_bonos" => ['administrador'],

  "configuracion_correo" => ['administrador'],
  "configuracion_pdf" => ['administrador'],

  "agregar_etiqueta" => ['administrador'],
  "eliminar_etiqueta" => ['administrador'],
  "cargar_etiquetas" => ['administrador'],

  "configuracion_printer" => ['administrador'],
  "actualizar_printer" => ['administrador'],
  "cargar_printers" => ['administrador'],
  "eliminar_printer" => ['administrador'],

  "subir_logo" => ['administrador'],
  "borrar_imagen" => ['administrador'],
  "guardar_datos" => ['administrador']
];

// Chequear permisos
if (isset($_POST['action'])) {
  check_permission_action($_POST['action'], $permissions);
}


switch ($action) {

  // Devuelve tanto la fecha y hora (completa) y solo la fecha
  case 'fecha_actual':

    $sql = "SELECT NOW() AS fecha_completa, CURDATE() AS fecha";
    $result = $db->query($sql);
    $row = $result->fetch_assoc();

    // Devuelve ambas fechas, para que JS decida cuál usar
    echo json_encode($row);  // Devuelve ambas fechas en formato JSON
    exit;
    break;
  case 'configuracion_de_impresion':

    $sql = "SELECT * FROM printer_settings WHERE usuario_id = '$user_id'; 
            SELECT * FROM configuraciones;";

    echo jsonMultiQueryResult($db, $sql);
    exit;

    break;
  // Mostrar todos los bonos
  case 'index_bonos':
    handleDataTableRequest($db, [
      'columns' => ['nombre', 'apellidos', 'valor', 'fecha', 'bono_id', 'nombre_usuario', 'apellidos_usuario'],
      'searchable' => ['nombre', 'apellidos', 'valor', 'fecha', 'bono_id', 'nombre_usuario', 'apellidos_usuario'],
      'base_table' => 'bonos b',
      'table_with_joins' => 'bonos b INNER JOIN clientes c ON b.cliente_id = c.cliente_id
        INNER JOIN usuarios u ON b.usuario_id = u.usuario_id',
      'select' => 'SELECT c.nombre as nombre, c.apellidos as apellidos, b.valor as valor,
        b.fecha as fecha, b.bono_id as bono_id, u.nombre as nombre_usuario, u.apellidos as apellidos_usuario',
      'table_rows' => function ($row) {
        return [
          'id' => $row['bono_id'],
          'cliente' => ucwords($row['nombre']) . ' ' . ucwords($row['apellidos']),
          'valor' => number_format($row['valor'], 2),
          'usuario' => ucwords($row['nombre_usuario']) . ' ' . ucwords($row['apellidos_usuario']),
          'fecha' => $row['fecha'],
          'acciones' => '
        <span class="btn-action action-danger" onclick="deleteBond(\'' . $row['bono_id'] . '\')" title="Eliminar">
            ' . BUTTON_DELETE . '
        </span>'
        ];
      }
    ]);
    break;

  // Configuracion de bonos
  case 'configuracion_bonos':

    $params = [
      (int)$user_id,
      $_POST['min'],
      $_POST['value'],
      (int)$_POST['status']
    ];

    echo handleProcedureAction($db, ' cf_configuracion_bonos', $params);
    break;

  // Configuracion para los correos electronicos
  case 'configuracion_correo':

    $params = [
      $_POST['email'],
      $_POST['password'],
      $_POST['host'],
      $_POST['smtps'],
      $_POST['port']
    ];

    echo handleProcedureAction($db, 'cf_configuracion_correo', $params);

    break;
  // Configuracion para las facturas en pdf
  case 'configuracion_pdf':

    $params = [
      $_POST['policy'],
      $_POST['title']
    ];

    echo handleProcedureAction($db, 'cf_configuracionPDF', $params);
    break;

  // Crear nueva etiqueta
  case 'agregar_etiqueta':

    $params = [
      // Datos generales
      $_POST['nombre_config'],
      $_POST['descripcion'] ?? null,

      // Tamaño físico
      $_POST['ancho_mm'] !== '' ? $_POST['ancho_mm'] : null,
      $_POST['alto_mm'] !== '' ? $_POST['alto_mm'] : null,
      $_POST['orientacion'] ?? null,

      // Código de barras
      $_POST['tipo_barcode'] ?? null,
      $_POST['mostrar_texto_barcode'] ?? null,
      $_POST['barcode_font_size'] !== '' ? $_POST['barcode_font_size'] : null,
      $_POST['barcode_x'] !== '' ? $_POST['barcode_x'] : null,
      $_POST['barcode_y'] !== '' ? $_POST['barcode_y'] : null,
      $_POST['barcode_width'] !== '' ? $_POST['barcode_width'] : null,
      $_POST['barcode_height'] !== '' ? $_POST['barcode_height'] : null,

      // Descripción
      $_POST['mostrar_descripcion'] ?? null,
      $_POST['descripcion_font_size'] !== '' ? $_POST['descripcion_font_size'] : null,
      $_POST['descripcion_x'] !== '' ? $_POST['descripcion_x'] : null,
      $_POST['descripcion_y'] !== '' ? $_POST['descripcion_y'] : null,
      $_POST['descripcion_width'] !== '' ? $_POST['descripcion_width'] : null,
      $_POST['descripcion_height'] !== '' ? $_POST['descripcion_height'] : null,

      // Precio
      $_POST['mostrar_precio'] ?? null,
      $_POST['precio_font_size'] !== '' ? $_POST['precio_font_size'] : null,
      $_POST['precio_x'] !== '' ? $_POST['precio_x'] : null,
      $_POST['precio_y'] !== '' ? $_POST['precio_y'] : null,
      $_POST['precio_width'] !== '' ? $_POST['precio_width'] : null,
      $_POST['precio_height'] !== '' ? $_POST['precio_height'] : null,

      // Impresora
      $_POST['impresora'] ?? null,
      $_POST['activo'] ?? 1
    ];

    echo handleProcedureAction($db, 'cf_insertar_etiquetas', $params);
    break;
  case 'eliminar_etiqueta':

    echo handleDeletionAction($db, (int)$_POST['label_id'], 'cf_eliminar_etiqueta');

    break;
  case 'cargar_etiquetas':

    handleDataTableRequest($db, [
      'columns' => ['etiqueta_id', 'nombre_config', 'ancho_mm', 'alto_mm', 'impresora'],
      'searchable' => ['etiqueta_id', 'nombre_config', 'ancho_mm', 'alto_mm', 'impresora'],
      'base_table' => 'etiquetas',
      'table_with_joins' => 'etiquetas',
      'select' => 'SELECT etiqueta_id,nombre_config,ancho_mm,alto_mm,impresora',
      'table_rows' => function ($row) {

        // Acciones
        $acciones = '<a ';
        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          $acciones .= 'class="btn-action action-info" href="' . base_url . 'config/label_edit&id=' . $row['etiqueta_id'] . '"';
        } else {
          $acciones .= 'class="btn-action action-info action-disable" href="#"';
        }
        $acciones .= ' title="Editar">' . BUTTON_EDIT . '</a>';

        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          $acciones .= '<span class="btn-action action-danger erase_label" data-id="' . $row['etiqueta_id'] . '" data-name="' . ucwords($row['nombre_config']) . '" title="Eliminar">
        ' . BUTTON_DELETE . '</span>';
        } else {
          $acciones .= '<span class="btn-action action-danger action-disable" title="Eliminar">' . BUTTON_DELETE . '</span>';
        }

        return [
          "id" => $row['etiqueta_id'],
          "nombre" => ucwords($row['nombre_config']),
          "ancho" => $row['ancho_mm'],
          "alto" => $row['alto_mm'],
          "impresora" => $row['impresora'],
          "acciones" => $acciones
        ];
      }
    ]);
    break;
  case 'configuracion_printer':

    $params = [
      $_POST['printer_name'] ?? null,
      $_POST['user_id'] ?? 0,
      $_POST['printer_type'] ?? null,
      $_POST['printer_language'] ?? null,
      $_POST['print_method'] ?? null,
      $_POST['paper_width'] ?? null,
      $_POST['copies'] ?? null,

      $_POST['auto_cut'] ?? 0,
      $_POST['open_cash_drawer'] ?? 0,
      $_POST['signature'] ?? 0,
      $_POST['policy_footer'] ?? '',
      $_POST['ticket_footer'] ?? '',

      $_POST['use_barcode'] ?? 0,
      $_POST['barcode_type'] ?? null,
      $_POST['barcode_height'] ?? null,
      $_POST['barcode_width'] ?? null,

      $_POST['use_qr'] ?? 0,
      $_POST['qr_size'] ?? null,
      $_POST['logo_density'] ?? null,

      $_POST['feed_start'] ?? 0,
      $_POST['feed_end'] ?? 0
    ];

    echo handleProcedureAction($db, 'cf_agregar_printer_settings', $params);
    break;
  case 'actualizar_printer':

    $params = [
      $_POST['printer_id'] ?? null,
      $_POST['printer_name'] ?? null,
      $_POST['user_id'] ?? 0,
      $_POST['printer_type'] ?? null,
      $_POST['printer_language'] ?? null,
      $_POST['print_method'] ?? null,
      $_POST['paper_width'] ?? null,
      $_POST['copies'] ?? null,

      $_POST['auto_cut'] ?? 0,
      $_POST['open_cash_drawer'] ?? 0,
      $_POST['signature'] ?? 0,
      $_POST['policy_footer'] ?? '',
      $_POST['ticket_footer'] ?? '',

      $_POST['use_barcode'] ?? 0,
      $_POST['barcode_type'] ?? null,
      $_POST['barcode_height'] ?? null,
      $_POST['barcode_width'] ?? null,

      $_POST['use_qr'] ?? 0,
      $_POST['qr_size'] ?? null,
      $_POST['logo_density'] ?? null,

      $_POST['feed_start'] ?? 0,
      $_POST['feed_end'] ?? 0
    ];

    echo handleProcedureAction($db, 'cf_actualizar_printer_settings', $params);
    break;
  case 'cargar_printers':

    handleDataTableRequest($db, [
      'columns' => ['nombre', 'printer_name', 'printer_type', 'printer_language', 'paper_width'],
      'searchable' => ['nombre', 'printer_name', 'printer_type', 'printer_language', 'paper_width'],
      'base_table' => 'printer_settings',
      'table_with_joins' => 'printer_settings p 
                            INNER JOIN usuarios u ON u.usuario_id = p.usuario_id',
      'select' => 'SELECT p.printer_id,concat(u.nombre," ",IFNULL(u.apellidos,"")) as nombre,
                  p.printer_name,p.printer_type,p.printer_language,p.paper_width',
      'table_rows' => function ($row) {

        // Acciones
        $acciones = '<a ';
        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          $acciones .= 'class="btn-action action-info" href="' . base_url . 'config/edit_printer&id=' . $row['printer_id'] . '"';
        } else {
          $acciones .= 'class="btn-action action-info action-disable" href="#"';
        }
        $acciones .= ' title="Editar">' . BUTTON_EDIT . '</a>';

        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          $acciones .= '<span class="btn-action action-danger erase_printer" data-id="' . $row['printer_id'] . '" data-name="' . ucwords($row['printer_name']) . '" title="Eliminar">
        ' . BUTTON_DELETE . '</span>';
        } else {
          $acciones .= '<span class="btn-action action-danger action-disable" title="Eliminar">' . BUTTON_DELETE . '</span>';
        }

        return [
          "usuario" => ucwords($row['nombre']),
          "printer" => $row['printer_name'],
          "tipo" => $row['printer_type'],
          "lenguaje" => $row['printer_language'],
          "tamaño" => $row['paper_width'],
          "acciones" => $acciones
        ];
      }
    ]);

    break;
  case 'eliminar_printer':

    echo handleDeletionAction($db, (int)$_POST['printer_id'], 'cf_eliminar_printer');

    break;
  case 'subir_logo':

    $response = array();  // Array para almacenar las respuestas
    $dir_name = '';
    $image_path = '';

    if (isset($config['carpeta']) && !empty($config['carpeta'])) {
      $dir_name = $config['carpeta'];
    }

    // Primero, verificamos si se está subiendo una imagen
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {

      // Definir la ruta donde se guardará la imagen 
      if ($_SERVER['SERVER_NAME'] === 'localhost') {
        // Si estamos en localhost, usar la ruta relativa
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/" . basename(dirname(__DIR__)) . "/public/uploads/" . $dir_name;
      } else {
        // Si no estamos en localhost, usar la ruta pública estándar para producción
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/public/uploads/" . $dir_name;
      }

      // Verificar si la carpeta existe, si no, crearla
      if (!file_exists($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
          $response['error'][] = "Hubo un error al intentar crear la carpeta: " . $target_dir;
        } else {
          $response['success'][] = "La carpeta fue creada con éxito: " . $target_dir;
        }
      } else {
        // Si la carpeta ya existe, devolver la ruta
        $response['info'][] = "La carpeta ya existe: " . $target_dir;
      }

      // Obtener el nombre del archivo y la extensión
      $file_name = basename($_FILES["logo"]["name"]);
      $target_file = $target_dir . $file_name;
      $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

      // Verificar si el archivo es una imagen
      if (getimagesize($_FILES["logo"]["tmp_name"]) === false) {
        $response['error'][] = "El archivo no es una imagen.";
        echo json_encode($response);
        exit;
      }

      // Verificar el tamaño de la imagen (2 MB máximo)
      if ($_FILES["logo"]["size"] > 2000000) {
        $response['error'][] = "Error archivo demasiado grande.";
        echo json_encode($response);
        exit;
      }

      // Verificar la extensión de la imagen
      $allowed_types = ["jpg", "jpeg", "png"];
      if (!in_array($imageFileType, $allowed_types)) {
        $response['error'][] = "Error solo se permiten imágenes JPG, JPEG, PNG";
        echo json_encode($response);
        exit;
      }

      // No comprimir ni convertir la imagen, solo moverla
      $random_name = "logo";  // Generar un nombre aleatorio para la imagen

      // Establecer la nueva ruta de la imagen con el nombre aleatorio
      $target_file = $target_dir . $random_name . '.' . $imageFileType;

      // Mover la imagen con el nuevo nombre aleatorio
      if (!move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
        $response['error'][] = "Error al mover el archivo a la carpeta de destino.";
        exit();
      }

      // Si la imagen se subió correctamente, proporcionar un mensaje
      $response['success'][] = "La imagen ha sido subida exitosamente: " . $target_file;

      // Establecer la ruta de la imagen para la base de datos
      $image_path = $dir_name . $random_name . '.' . $imageFileType;  // Ruta relativa a la carpeta de imágenes

      // Actualizar el producto en la base de datos con la ruta de la imagen
      $sql = "UPDATE configuraciones SET config_value = '$image_path' WHERE config_key like '%logo_path%'";
      if (mysqli_query($db, $sql)) {
        $response['success'][] = "Imagen subida con exito.";
      } else {
        $response['error'][] = "Error al actualizar en la base de datos: " . mysqli_error($db);
      }
    } else {
      $response['error'][] = "Hubo un error al subir la imagen.";
    }

    echo json_encode($response);  // Devolver todas las respuestas en formato JSON
    break;
  case "borrar_imagen":

    $response = [
      'success' => false,
      'deleted' => false,
      'message' => '',
      'debug'   => []
    ];

    // Obtener imagen guardada
    $query = $db->query("SELECT config_value FROM configuraciones WHERE config_key LIKE '%logo_path%'");

    if (!$query || !$row = $query->fetch_assoc()) {
      $response['message'] = 'Configuración no encontrada';
      echo json_encode($response);
      exit;
    }

    if (empty($row['config_value'])) {
      $response['message'] = 'No hay imagen para borrar';
      echo json_encode($response);
      exit;
    }

    // Ruta base
    $basePath = ($_SERVER['SERVER_NAME'] === 'localhost')
      ? $_SERVER['DOCUMENT_ROOT'] . '/' . basename(dirname(__DIR__)) . '/public/uploads/'
      : $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/';

    // Ruta relativa guardada en BD
    $relativePath = ltrim($row['config_value'], '/');

    // Nombre sin extensión y carpeta
    $imgName = pathinfo($relativePath, PATHINFO_FILENAME);
    $imgDir  = dirname($relativePath);

    // Buscar y borrar cualquier archivo con ese nombre (cualquier extensión)
    foreach (glob("{$basePath}{$imgDir}/{$imgName}.*") as $filePath) {

      $response['debug'][] = $filePath;

      if (is_file($filePath)) {
        unlink($filePath);
        $response['deleted'] = true;
      }
    }

    // Limpiar BD si se borró algo
    if ($response['deleted']) {
      $db->query("UPDATE configuraciones SET config_value = '' WHERE config_key LIKE '%logo_path%'");

      $response['success'] = true;
      $response['message'] = 'Imagen eliminada correctamente';
    } else {
      $response['message'] = 'No se encontró la imagen en disco';
    }

    echo json_encode($response);
    exit;
    break;
  case 'guardar_datos':
    $params = [
      $_POST['logo_url'],
      $_POST['slogan'],
      $_POST['site_name'],
      $_POST['address'],
      $_POST['mail'],
      $_POST['tel'],
    ];

    echo handleProcedureAction($db, 'cf_datos_del_sitio', $params);
    break;
}
