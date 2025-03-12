<?php
session_start();
require_once 'autoload.php';
require_once 'help.php';
require_once 'config/db.php';
require_once 'config/parameters.php';
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
