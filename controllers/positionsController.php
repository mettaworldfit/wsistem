<?php

require_once './models/positions.php';

class PositionsController {

    public function index(){

        $method = new Positions;
        $positions = $method->showPositions();
        
        require_once './views/positions/index.php';
    }
    
    public function add() {

        require_once './views/positions/add.php';
    }

    public function edit() {

        require_once './views/positions/edit.php';
    }

  
}