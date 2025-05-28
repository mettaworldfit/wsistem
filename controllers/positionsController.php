<?php

class PositionsController {

    public function index(){

        require_once './views/positions/index.php';
    }
    
    public function add() {

        require_once './views/positions/add.php';
    }

    public function edit() {

        require_once './views/positions/edit.php';
    }

  
}