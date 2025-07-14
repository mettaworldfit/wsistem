<?php
$uri = $_SERVER["REQUEST_URI"];

// Scripts globales
$globalScripts = [
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
    'invoices/addpurchase' => [
        base_url . 'public/functions/pieces.js',
        base_url . 'public/functions/products.js',
        base_url . 'public/functions/services.js',
        base_url . 'public/functions/contacts.js',
        base_url . 'public/functions/price_lists.js',
    ],
    // ... (resto del mapeo como antes)
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
    echo '<script src="' . $src . '" type="text/javascript"></script>' . PHP_EOL;
}

// Imprimir scripts específicos
foreach ($matchedScripts as $src) {
    echo '<script src="' . $src . '" type="text/javascript"></script>' . PHP_EOL;
}
?>
