<?php

/**
 * Auto cargar las rutas de todos los controladores del sistemas
 */

function controllers_autoload($classname)
{
    foreach (glob('src/modules/*', GLOB_ONLYDIR) as $module) {

        $file = $module . '/' . $classname . '.php';

        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    require_once 'views/layout/404.php';
}

spl_autoload_register('controllers_autoload');