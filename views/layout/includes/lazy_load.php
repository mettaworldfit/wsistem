<?php

// Version 

function versioned_js($path)
{
    // Detectar si estamos en producción (ajusta el dominio real)
    $is_production = ($_SERVER['HTTP_HOST'] !== 'localhost');

    // Si NO estamos en producción → devolver tal cual (sin versión)
    if (!$is_production) {
        return $path;
    }

     // Si contiene el dominio local, sí versiona
    if (strpos($path, 'http') === 0 && strpos($path, $_SERVER['HTTP_HOST']) === false) {
        return $path;
    }

    // En producción y archivo local → agregar versión
    return $path . '?v=' . APP_VERSION;
}


$uri = $_SERVER["REQUEST_URI"];

// Scripts globales
$globalScripts = [
    base_url . 'public/functions/pos.js',
    base_url . 'public/functions/users.js',
    base_url . 'public/functions/invoices.js',
    base_url . 'public/functions/workshop.js',
];

// Scripts específicos por ruta
$scriptsMap = [
    'home' => [
        'https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js',
        base_url . 'public/functions/home.js',
        base_url . 'public/functions/reports.js',
    ],

    // Invoices: compras, edición, cotización, órdenes
    'invoices/addpurchase' => [
        base_url . 'public/functions/pieces.js',
        base_url . 'public/functions/products.js',
        base_url . 'public/functions/services.js',
        base_url . 'public/functions/contacts.js',
        base_url . 'public/functions/price_lists.js',
    ],
    'invoices/edit' => [
        base_url . 'public/functions/pieces.js',
        base_url . 'public/functions/products.js',
        base_url . 'public/functions/services.js',
        base_url . 'public/functions/contacts.js',
        base_url . 'public/functions/price_lists.js',
    ],
    'invoices/quote' => [
        base_url . 'public/functions/pieces.js',
        base_url . 'public/functions/products.js',
        base_url . 'public/functions/services.js',
        base_url . 'public/functions/contacts.js',
        base_url . 'public/functions/price_lists.js',
    ],
    'invoices/add_order' => [
        base_url . 'public/functions/pieces.js',
        base_url . 'public/functions/products.js',
        base_url . 'public/functions/services.js',
        base_url . 'public/functions/contacts.js',
        base_url . 'public/functions/price_lists.js',
    ],

    // Reparación específica
    'invoices/repair_edit' => [
        base_url . 'public/functions/pieces.js',
        base_url . 'public/functions/services.js',
        base_url . 'public/functions/contacts.js',
        base_url . 'public/functions/repair.js',
        base_url . 'public/functions/price_lists.js',
    ],

    // Vista de órdenes, reparaciones o facturas
    'invoices/index' => [
        base_url . 'public/functions/price_lists.js',
        base_url . 'public/functions/repair.js',
        base_url . 'public/functions/contacts.js',
        base_url . 'public/functions/services.js',
        base_url . 'public/functions/pieces.js',
    ],
    'invoices/addrepair' => [
        base_url . 'public/functions/price_lists.js',
        base_url . 'public/functions/repair.js',
        base_url . 'public/functions/contacts.js',
        base_url . 'public/functions/services.js',
        base_url . 'public/functions/pieces.js',
    ],
    'invoices/index_repair' => [
        base_url . 'public/functions/price_lists.js',
        base_url . 'public/functions/repair.js',
        base_url . 'public/functions/contacts.js',
        base_url . 'public/functions/services.js',
        base_url . 'public/functions/pieces.js',
    ],
    'invoices/orders' => [
        base_url . 'public/functions/price_lists.js',
        base_url . 'public/functions/repair.js',
        base_url . 'public/functions/contacts.js',
        base_url . 'public/functions/services.js',
        base_url . 'public/functions/pieces.js',
    ],

    // Productos y piezas
    'products/index' => [
        base_url . 'public/functions/products.js',
        base_url . 'public/functions/price_lists.js',
    ],
    'products' => [
        base_url . 'public/functions/products.js',
        base_url . 'public/functions/price_lists.js',
    ],
    'pieces/index' => [
        base_url . 'public/functions/pieces.js',
        base_url . 'public/functions/price_lists.js',
    ],
    'pieces' => [
        base_url . 'public/functions/pieces.js',
        base_url . 'public/functions/price_lists.js',
    ],

    // Taller
    'workshop/index' => [
        base_url . 'public/functions/contacts.js',
        base_url . 'public/functions/repair.js',
        base_url . 'public/functions/pieces.js',
        base_url . 'public/functions/services.js',
    ],

    // Otros módulos
    'warehouses' => [
        base_url . 'public/functions/warehouses.js',
    ],
    'categories' => [
        base_url . 'public/functions/categories.js',
    ],
    'taxes' => [
        base_url . 'public/functions/taxes.js',
    ],
    'contacts' => [
        base_url . 'public/functions/contacts.js',
    ],
    'reports/day' => [
        base_url . 'public/functions/repair.js',
        base_url . 'public/functions/payments.js',
        base_url . 'public/functions/reports.js',
    ],
    'price_lists' => [
        base_url . 'public/functions/price_lists.js',
    ],
];


// Buscar coincidencia
$matchedScripts = [];

foreach ($scriptsMap as $pattern => $scripts) {
    if (str_contains($uri, $pattern)) {
        $matchedScripts = $scripts;
        break;
    }
}

// Scripts por defecto si no hay coincidencia
if (empty($matchedScripts)) {
    $matchedScripts = [
        base_url . 'public/functions/positions.js',
        base_url . 'public/functions/offers.js',
        base_url . 'public/functions/bills.js',
        base_url . 'public/functions/payments.js',
        base_url . 'public/functions/config.js',
        base_url . 'public/functions/reports.js',
        base_url . 'public/functions/services.js',
    ];
}

// Imprimir scripts globales
foreach ($globalScripts as $src) {
    echo '<script src="' . versioned_js($src) . '" type="text/javascript"></script>' . PHP_EOL;
}

foreach ($matchedScripts as $src) {
    echo '<script src="' . versioned_js($src) . '" type="text/javascript"></script>' . PHP_EOL;
}
