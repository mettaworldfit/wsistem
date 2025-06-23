<?php

// Configurar sesión
ini_set('session.gc_maxlifetime', 4800);
session_start();

$timeout = 4800;

require_once 'config/parameters.php';

// Verificar si el usuario ha iniciado sesión
$current_path = $_SERVER['REQUEST_URI'];
$login_url = base_url . 'users/login';

if (!isset($_SESSION['identity']) && !str_contains($current_path, '/users/login')) {
    header("Location: $login_url");
    exit;
}

// Verificar si ha habido inactividad prolongada
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout) {
    session_unset();
    session_destroy();
    header("Location: $login_url&timeout=1");
    exit;
}

// Actualiza el tiempo de última actividad
$_SESSION['LAST_ACTIVITY'] = time();

require_once 'autoload.php';
require_once 'help.php';
require_once 'config/db.php';
require_once 'views/layout/header.php';

function NO_LOGIN()
{
    $CONTROLLER_NAME = NO_LOGIN_CONTROLLER;
    $ACTION = NO_LOGIN_ACTION;

    $CLASSNAME = new $CONTROLLER_NAME();
    $CLASSNAME->$ACTION();
}

function PAGE_INDEX($CONTROLLER_NAME)
{
    if (class_exists($CONTROLLER_NAME)) {


        $CLASSNAME = new $CONTROLLER_NAME();

        if (isset($_GET['action']) && method_exists($CLASSNAME, $_GET['action'])) {
            // Solo si el CONTROLLER y el ACTION son correctos

            $ACTION = $_GET['action'];
            $CLASSNAME->$ACTION();
        } else if (isset($_GET['action'])) {
            // Si el ACTION no existe

            $DEFAULT_CONTROLLER = DEFAULT_CONTROLLER;
            $NO_FOUND = NO_FOUND;

            $CLASSNAME = new $DEFAULT_CONTROLLER();

            $CLASSNAME->$NO_FOUND();
        }
    }
}



/**
 *  Control de las rutas */


if (isset($_GET['controller']) && isset($_GET['action'])) {

    if (isset($_SESSION['admin']) || isset($_SESSION['identity'])) {

        $CONTROLLER_NAME = $_GET['controller'] . 'Controller';
        PAGE_INDEX($CONTROLLER_NAME);
    } else {
        NO_LOGIN();
    }
} else if (!isset($_GET['controller']) || !isset($_GET['action'])) {

    // Sin datos en la URL

    if (isset($_SESSION['admin']) || isset($_SESSION['identity'])) {

        $CONTROLLER_NAME = DEFAULT_CONTROLLER;
        $ACTION = DEFAULT_ACTION;

        $CLASSNAME = new $CONTROLLER_NAME();
        $CLASSNAME->$ACTION();
    } else {

        // Usuario no logeado
        NO_LOGIN();
    }
}



require_once 'views/layout/footer.php';
