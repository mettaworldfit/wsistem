<?php

require_once './models/services.php';

class ServicesController {

    public function index(){
        
        $model = new Services();
        $services = $model->showServices();
        
        require_once './views/services/index.php';
    }
    
    public function add() {

        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            require_once './views/services/add.php';

        } else {
             // Permiso denegado
             require_once './views/layout/denied.php';
        }
       
    }

    public function edit() {

        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            require_once './views/services/edit.php';

        } else {
             // Permiso denegado
             require_once './views/layout/denied.php';
        }
      
    }

  
}

