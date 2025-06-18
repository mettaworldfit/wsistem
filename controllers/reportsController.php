<?php

class ReportsController
{

    public function day()
    {

        $invoices = Help::calculateSalesToDay();

        require_once './views/reports/day.php';
    }

    public function querys()
    {

        require_once './views/reports/querys.php';
    }

    public function cash_closing()
    {
        if ($_SESSION['identity']->nombre_rol == 'administrador') {

            $cashOpening = Help::getCashOpening(); // Obtener datos de la caja abierta

            $cash = Help::getDailySalesByPaymentMethod(1); // Efectivo
            $credit = Help::getDailySalesByPaymentMethod(2); // Tarjeta de credido
            $debit = Help::getDailySalesByPaymentMethod(3); // Tarjeta de debito
            $card = $credit + $debit;
            $transfers = Help::getDailySalesByPaymentMethod(4); // Tranferencias
            $checks = Help::getDailySalesByPaymentMethod(5); // Cheques
            $totalExpenses = Help::getExpensesToday(); // Total gastado hoy

            require_once './views/reports/cash_closing.php';
        } else {
            // Permiso denegado
            require_once './views/layout/denied.php';
        }
    }
}
