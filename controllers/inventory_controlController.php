<?php

require_once './models/inventory_control.php';

class Inventory_controlController
{

    public function inventory()
    {
        // Verificar rol de usuario
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            $symbol = "DOP";

            $model = new Inventory_control();

            $inventory = $model->show_inventory();

            // Calcular el total del inventario

            $inventoryValue = $model->InventoryValue();
            $data = $inventoryValue->fetch_object();

            $value = number_format($data->total, 2);
            $bruto = number_format($data->bruto, 2);


            require_once './views/inventory_control/inventory.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }
}
