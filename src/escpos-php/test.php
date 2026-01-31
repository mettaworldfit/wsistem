<?php

require_once '../../vendor/escpos-php/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

$connector = new WindowsPrintConnector("POS-80");
$printer   = new Printer($connector);

$printer->text("Prueba OK\n");
$printer->cut();
$printer->close();