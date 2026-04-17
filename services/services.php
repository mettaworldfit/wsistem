<?php

require_once '../config/db.php';
require_once '../config/parameters.php';
require_once 'functions/functions.php';
session_start();
$db = Database::connect();
$config = Database::getConfig();
$user_id = $_SESSION['identity']->usuario_id;
$action = $_POST['action'] ?? null;

require_once __DIR__ . '/../vendor/autoload.php';

use Tinify\Tinify;

// API key por defecto
$defaultTinifyKey = "j6NYhPDxKlPVz6C0NyXrr5c6vVjNKvqj";

// Obtener desde config si existe y no está vacía
$tinify_API_KEY = !empty($config['tinify_API_KEY'])
  ? $config['tinify_API_KEY']
  : $defaultTinifyKey;

// Setear la key
Tinify::setKey($tinify_API_KEY);

$permissions = [
  "index_servicios" => [], // Todos tienen permiso
  "buscar_servicios" => [],
  "agregar_servicio" => [],
  "actualizar_servicio" => [],
  "eliminar_servicio" => ['administrador'],
  "subir_imagen" => [],
  "borrar_imagen" => []
];

// Chequear permisos
if (isset($_POST['action'])) {
  check_permission_action($_POST['action'], $permissions);
}


switch ($action) {

  // Cargar tabla de servicios
  case 'index_servicios':
    handleDataTableRequest($db, [
      'columns' => ['nombre_servicio', 'precio', 'costo', 'servicio_id'],
      'searchable' => ['nombre_servicio', 'precio', 'costo'],
      'base_table' => 'servicios',
      'table_with_joins' => 'servicios',
      'select' => 'SELECT servicio_id, nombre_servicio,costo ,precio',
      'table_rows' => function ($row) {

        $acciones = '<td>';
        // Editar
        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          $acciones .= '<a href="' . base_url . 'services/edit&id=' . $row['servicio_id'] . '">
                  <span class="action-info btn-action">' . BUTTON_EDIT . '</span>
                </a>';
        } else {
          $acciones .= '<a href="#">
                  <span class="action-info btn-action action-disable">' . BUTTON_EDIT . '</span>
                </a>';
        }

        // Eliminar
        if ($_SESSION['identity']->nombre_rol == 'administrador') {
          $acciones .= '<span class="action-danger btn-action" onclick="deleteService(\'' . $row['servicio_id'] . '\')" title="Eliminar">
                  ' . BUTTON_DELETE . '
                </span>';
        } else {
          $acciones .= '<span class="action-danger btn-action action-disable" title="Eliminar">
                  ' . BUTTON_DELETE . '
                </span>';
        }

        $acciones .= '</td>';

        return [
          'servicio_id' => "<td>{$row['servicio_id']}</td>",
          'nombre_servicio' => "<td>" . htmlspecialchars($row['nombre_servicio']) . "</td>",
          'costo' => "<td>" . number_format($row['costo'] ?? 0, 2) . "</td>",
          'precio' => "<td>" . number_format($row['precio'] ?? 0, 2) . "</td>",
          'acciones' => $acciones
        ];
      }
    ]);
    break;

  // Buscar servicio por ID
  case 'buscar_servicios':
    $id = $_POST['service_id'];
    jsonQueryResult($db, "SELECT * FROM servicios WHERE servicio_id = '$id'");
    break;

  // Agregar servicio
  case 'agregar_servicio':
    $params = [
      $_SESSION['identity']->usuario_id,
      $_POST['name'] ?? '',
      $_POST['cost'] ?? 0,
      $_POST['price'] ?? 0
    ];
    echo handleProcedureAction($db, 'sv_agregarServicio', $params);
    break;

  // Actualizar servicio
  case 'actualizar_servicio':
    $params = [
      $_POST['service_id'],
      $_POST['name'] ?? '',
      $_POST['cost'] ?? 0,
      $_POST['price'] ?? 0
    ];
    echo handleProcedureAction($db, 'sv_actualizarServicio', $params);
    break;

  // Eliminar servicio
  case 'eliminar_servicio':
    echo handleProcedureAction($db, 'sv_eliminarServicio', [(int)$_POST['service_id']]);
    break;

  // Subir imagen
  case 'subir_imagen':

    $response = array();  // Array para almacenar las respuestas
    $dir_name = '';
    $image_path = '';

    if (empty($config['carpeta'])) {
      $response['error'][] = "No tienes esta funcion habilitada";
      exit;
    }

    $dir_name = $config['carpeta'];

    // Primero, verificamos si se está subiendo una imagen
    if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] === UPLOAD_ERR_OK) {
      $service_id = $_POST['service_id'];  // El ID del servicios que se acaba de crear

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
      $file_name = basename($_FILES["service_image"]["name"]);
      $target_file = $target_dir . $file_name;
      $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

      // Verificar si el archivo es una imagen
      if (getimagesize($_FILES["service_image"]["tmp_name"]) === false) {
        $response['error'][] = "El archivo no es una imagen.";
        echo json_encode($response);
        exit;
      }

      // Verificar el tamaño de la imagen (2 MB máximo)
      if ($_FILES["service_image"]["size"] > 2000000) {
        $response['error'][] = "Error archivo demasiado grande.";
        echo json_encode($response);
        exit;
      }

      // Verificar la extensión de la imagen
      $allowed_types = ["jpg", "jpeg", "png", "webp", "avif", "jfif"];
      if (!in_array($imageFileType, $allowed_types)) {
        $response['error'][] = "Error solo se permiten imágenes JPG, JPEG, PNG, AVIF, WEBP, JFIF.";
        echo json_encode($response);
        exit;
      }

      // Generar un nombre aleatorio de 11 caracteres numéricos
      function generate_random_filename($length = 11)
      {
        return substr(uniqid(rand(), true), 0, $length);  // Extraer solo los primeros 11 caracteres
      }

      // Si estamos en producción (no localhost)
      if ($_SERVER['SERVER_NAME'] !== 'localhost') {
        // Comprimir y convertir la imagen usando Tinify solo si estamos en producción
        try {
          // Comprimir la imagen utilizando Tinify
          $source = \Tinify\Source::fromFile($_FILES["service_image"]["tmp_name"]);

          // Generar el nombre aleatorio para la imagen
          $random_name = generate_random_filename(); // Nombre aleatorio de 11 caracteres

          // Convertir la imagen a WebP si no es ya WebP, JFIF o AVIF
          if ($imageFileType !== 'webp' && $imageFileType !== 'avif' && $imageFileType !== 'jfif') {
            // Establecer la ruta del archivo WebP con el nombre aleatorio
            $target_file_webp = $target_dir . $random_name . '.webp';

            // Comprimir y guardar la imagen en WebP
            $source->toFile($target_file_webp);
            $response['success'][] = "La imagen ha sido comprimida y convertida a WebP con éxito en: " . $target_file_webp;

            // Establecer la ruta de la imagen para la base de datos
            $image_path = $dir_name . $random_name . '.webp';  // Ruta relativa a la carpeta de imágenes

          } else {
            // Si la imagen ya es WebP, JFIF o AVIF, guardarla tal cual
            move_uploaded_file($_FILES["service_image"]["tmp_name"], $target_file);
            $response['success'][] = "La imagen ha sido subida exitosamente: " . $target_file;

            // Establecer la ruta de la imagen para la base de datos
            $image_path = $dir_name . pathinfo($file_name, PATHINFO_FILENAME) . '.' . $imageFileType;  // Ruta relativa a la carpeta de imágenes
          }
        } catch (Exception $e) {
          $response['error'][] = "Hubo un error al comprimir y convertir la imagen: " . $e->getMessage();
          echo json_encode($response);
          exit;
        }

        // En localhost
      } else {
        // No comprimir la imagen, solo moverla si es AVIF, JFIF o WEBP
        if ($imageFileType === 'webp' || $imageFileType === 'avif' || $imageFileType === 'jfif') {

          // Obtener la extensión del archivo
          $imageFileType = strtolower(pathinfo($_FILES["service_image"]["name"], PATHINFO_EXTENSION));
          $random_name = generate_random_filename();  // Generar un nombre aleatorio para la imagen

          // Establecer la nueva ruta de la imagen con el nombre aleatorio
          $target_file = $target_dir . $random_name . '.' . $imageFileType;

          // Mover la imagen con el nuevo nombre aleatorio
          if (!move_uploaded_file($_FILES["service_image"]["tmp_name"], $target_file)) {
            $response['error'][] = "Error al mover el archivo a la carpeta de destino.";
            exit();
          }

          // Si la imagen se subió correctamente, proporcionar un mensaje
          $response['success'][] = "La imagen ha sido subida exitosamente: " . $target_file;

          // Establecer la ruta de la imagen para la base de datos
          $image_path = $dir_name . $random_name . '.' . $imageFileType;  // Ruta relativa a la carpeta de imágenes

        } else {
          // Si la imagen no es AVIF,WEBP ni JFIF, convertirla a WebP
          $image_tmp = $_FILES["service_image"]["tmp_name"];
          $image_info = getimagesize($image_tmp);
          $image_type = $image_info[2];

          if ($image_type == IMAGETYPE_JPEG || $image_type == IMAGETYPE_PNG) {
            $random_name = generate_random_filename(); // Generar un nombre aleatorio para la imagen
            $image = imagecreatefromstring(file_get_contents($image_tmp));
            $webp_file = $target_dir . $random_name . '.webp';
            imagewebp($image, $webp_file); // Guardar la imagen como WebP
            imagedestroy($image);
            $response['success'][] = "La imagen ha sido convertida a WebP y subida: " . $webp_file;

            // Establecer la ruta de la imagen para la base de datos
            $image_path = $dir_name . $random_name . '.webp';  // Ruta relativa a la carpeta de imágenes
          }
        }
      }

      // Actualizar el producto en la base de datos con la ruta de la imagen
      $sql = "UPDATE servicios SET imagen = '$image_path' WHERE servicio_id = '$service_id'";
      if (mysqli_query($db, $sql)) {
        $response['success'][] = "Servicio actualizado con la imagen.";
      } else {
        $response['error'][] = "Error al actualizar el servicio en la base de datos: " . mysqli_error($db);
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
      'debug' => []
    ];

    $service_id = $_POST['service_id'];

    // Obtener imagen guardada
    $oldImgQuery = $db->query("SELECT imagen FROM servicios WHERE servicio_id = '$service_id'");

    if ($oldImgQuery && $oldRow = mysqli_fetch_assoc($oldImgQuery)) {

      if (!empty($oldRow['imagen'])) {

        // Ruta base
        if ($_SERVER['SERVER_NAME'] === 'localhost') {
          $basePath = $_SERVER['DOCUMENT_ROOT'] . "/" . basename(dirname(__DIR__)) . "/public/uploads/";
        } else {
          $basePath = $_SERVER['DOCUMENT_ROOT'] . "/public/uploads/";
        }

        $relativePath = ltrim($oldRow['imagen'], '/');

        // Nombre sin extensión
        $imgName = pathinfo($relativePath, PATHINFO_FILENAME);
        $imgDir  = dirname($relativePath);

        $extensions = ["jpg", "jpeg", "png", "webp", "avif", "jfif"];

        foreach ($extensions as $ext) {
          $filePath = $basePath . $imgDir . '/' . $imgName . '.' . $ext;

          $response['debug'][] = $filePath;

          if (file_exists($filePath)) {
            unlink($filePath);
            $response['deleted'] = true;
          }
        }

        if ($response['deleted']) {

          $db->query("UPDATE servicios SET imagen = '' WHERE servicio_id = '$service_id'");

          $response['success'] = true;
          $response['message'] = 'Imagen eliminada correctamente';
        } else {
          $response['message'] = 'No se encontró la imagen en disco';
        }
      } else {
        $response['message'] = 'El servicio no tiene imagen';
      }
    } else {
      $response['message'] = 'Servicio no encontrado';
    }

    echo json_encode($response);
    exit;

    break;
}
