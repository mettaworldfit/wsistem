<?php

class PiecesController {

    public function index(){       
        require_once './views/pieces/index.php';
    }
    
    public function add() {

        require_once './views/pieces/add.php';
    }

    public function edit() {
        $id = $_GET['id'];

        $piece = Help::showPiecesID($_GET['id']);

        require_once './views/pieces/edit.php';
    }

  
}