<?php

/**
 * Auto cargar las rutas de todos los controladores del sistemas
 */

function controllers_autoload($classname){

	$filename = $classname . '.php';
	$file = 'controllers/' . $filename;

	if (file_exists($file) != false) {
		include ($file);
	} else {
		require_once "views/layout/404.php";
	}
	
}

spl_autoload_register('controllers_autoload');

