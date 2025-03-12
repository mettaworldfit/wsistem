<?php

require_once './models/pieces.php';

class PiecesController {

    public function index(){

        $method = new Pieces;
        $pieces = $method->showPieces();
        
        require_once './views/pieces/index.php';
    }
    
    public function add() {

        require_once './views/pieces/add.php';
    }

    public function edit() {

        require_once './views/pieces/edit.php';
    }

  
}