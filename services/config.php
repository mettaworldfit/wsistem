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

$permissions = [
  "fecha_actual" => [],

  "index_bonos" => ['administrador'],
  "configuracion_bonos" => ['administrador'],

  "configuracion_correo" => ['administrador'],
  "configuracion_pdf" => ['administrador'],

  "agregar_etiqueta" => ['administrador'],
  "eliminar_etiqueta" => ['administrador'],
  "cargar_etiquetas" => ['administrador']
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
      $_POST['logo'],
      $_POST['company'],
      $_POST['email'],
      $_POST['password'],
      $_POST['host'],
      $_POST['smtps'],
      $_POST['port'],
      $_POST['facebook'],
      $_POST['whatsapp'],
      $_POST['instagram'],
    ];

    echo handleProcedureAction($db, 'cf_configuracion_correo', $params);

    break;
  case 'configuracion_pdf':

    $params = [
      $_POST['logo'],
      $_POST['slogan'],
      $_POST['address'],
      $_POST['tel'],
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
     
    echo handleDeletionAction($db,(int)$_POST['label_id'],'cf_eliminar_etiqueta');

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
          $acciones .= '<span class="btn-action action-danger erase_label" data-id="'.$row['etiqueta_id'].'" data-name="'.ucwords($row['nombre_config']).'" title="Eliminar">
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
}
