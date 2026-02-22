<?php

// ==================================
// Versionado de JS
// ==================================
function versioned_js($path)
{
    $is_production = ($_SERVER['HTTP_HOST'] !== 'localhost');

    if (!$is_production) {
        return $path;
    }

    if (strpos($path, 'http') === 0 && strpos($path, $_SERVER['HTTP_HOST']) === false) {
        return $path;
    }

    return $path . '?v=' . APP_VERSION;
}

// ==================================
// Render de <script>
// ==================================
function print_script($script)
{
    $src  = versioned_js($script['src']);
    $type = $script['type'] ?? 'text/javascript';

    if ($type === 'module') {
        echo '<script type="module" src="' . $src . '"></script>' . PHP_EOL;
    } else {
        echo '<script type="text/javascript" src="' . $src . '"></script>' . PHP_EOL;
    }
}

// ==================================
// URI actual
// ==================================
$uri = $_SERVER["REQUEST_URI"];

// ==================================
// Scripts globales
// ==================================
$globalScripts = [
    [
        'src'  => base_url . 'public/functions/users.js',
        'type' => 'module'
    ],
    [
        'src'  => base_url . 'public/functions/invoices.js',
        'type' => 'module'
    ],
    [
        'src'  => base_url . 'public/functions/workshop.js',
        'type' => 'text/javascript'
    ],
    [
        'src'  => base_url . 'public/functions.js',
        'type' => 'module'
    ],
    [
        'src'  => base_url . 'public/test.js',
        'type' => 'module'
    ],
];

// ==================================
// Scripts por ruta (TODOS)
// ==================================
$scriptsMap = [

    'home' => [
        [
            'src'  => 'https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js',
            'type' => 'text/javascript'
        ],
        [
            'src'  => base_url . 'public/functions/home.js',
            'type' => 'module'
        ],
        [
            'src'  => base_url . 'public/functions/reports.js',
            'type' => 'module'
        ]
    ],

    'invoices/addpurchase' => [
        ['src' => base_url . 'public/functions/pieces.js',],
        ['src' => base_url . 'public/functions/products.js'],
        ['src' => base_url . 'public/functions/services.js'],
        ['src' => base_url . 'public/functions/contacts.js'],
        ['src' => base_url . 'public/functions/price_lists.js'],
    ],

    'invoices/edit' => [
        ['src' => base_url . 'public/functions/pieces.js'],
        ['src' => base_url . 'public/functions/products.js'],
        ['src' => base_url . 'public/functions/services.js'],
        ['src' => base_url . 'public/functions/contacts.js'],
        ['src' => base_url . 'public/functions/price_lists.js'],
    ],

    'invoices/quote' => [
        ['src' => base_url . 'public/functions/pieces.js'],
        ['src' => base_url . 'public/functions/products.js'],
        ['src' => base_url . 'public/functions/services.js'],
        ['src' => base_url . 'public/functions/contacts.js'],
        ['src' => base_url . 'public/functions/price_lists.js'],
    ],

    'invoices/add_order' => [
        ['src' => base_url . 'public/functions/pieces.js'],
        ['src' => base_url . 'public/functions/products.js'],
        ['src' => base_url . 'public/functions/services.js'],
        ['src' => base_url . 'public/functions/contacts.js'],
        ['src' => base_url . 'public/functions/price_lists.js'],
    ],

    'invoices/pos' => [
        ['src' => base_url . 'public/functions/reports.js', 'type' => 'module'],
        ['src' => base_url . 'public/functions/pos.js', 'type' => 'module'],
        ['src' => base_url . 'public/functions/pieces.js'],
        ['src' => base_url . 'public/functions/products.js'],
        ['src' => base_url . 'public/functions/services.js'],
        ['src' => base_url . 'public/functions/contacts.js'],
        ['src' => base_url . 'public/functions/price_lists.js'],
    ],

    'invoices/repair_edit' => [
        ['src' => base_url . 'public/functions/pieces.js'],
        ['src' => base_url . 'public/functions/services.js'],
        ['src' => base_url . 'public/functions/contacts.js'],
        ['src' => base_url . 'public/functions/repair.js'],
        ['src' => base_url . 'public/functions/price_lists.js'],
    ],

    'invoices/index' => [
        ['src' => base_url . 'public/functions/price_lists.js'],
        ['src' => base_url . 'public/functions/repair.js'],
        ['src' => base_url . 'public/functions/contacts.js'],
        ['src' => base_url . 'public/functions/services.js'],
        ['src' => base_url . 'public/functions/pieces.js'],
    ],

    'invoices/addrepair' => [
        ['src' => base_url . 'public/functions/price_lists.js'],
        ['src' => base_url . 'public/functions/repair.js'],
        ['src' => base_url . 'public/functions/contacts.js'],
        ['src' => base_url . 'public/functions/services.js'],
        ['src' => base_url . 'public/functions/pieces.js'],
    ],

    'invoices/index_repair' => [
        ['src' => base_url . 'public/functions/price_lists.js'],
        ['src' => base_url . 'public/functions/repair.js'],
        ['src' => base_url . 'public/functions/contacts.js'],
        ['src' => base_url . 'public/functions/services.js'],
        ['src' => base_url . 'public/functions/pieces.js'],
    ],

    'invoices/orders' => [
        ['src' => base_url . 'public/functions/price_lists.js'],
        ['src' => base_url . 'public/functions/repair.js'],
        ['src' => base_url . 'public/functions/contacts.js'],
        ['src' => base_url . 'public/functions/services.js'],
        ['src' => base_url . 'public/functions/pieces.js'],
    ],

    'products/index' => [
        ['src' => base_url . 'public/functions/products.js'],
        ['src' => base_url . 'public/functions/price_lists.js'],
    ],

    'products' => [
        ['src' => base_url . 'public/functions/products.js'],
        ['src' => base_url . 'public/functions/price_lists.js'],
    ],

    'pieces/index' => [
        ['src' => base_url . 'public/functions/pieces.js'],
        ['src' => base_url . 'public/functions/price_lists.js'],
    ],

    'pieces' => [
        ['src' => base_url . 'public/functions/pieces.js'],
        ['src' => base_url . 'public/functions/price_lists.js'],
    ],

    'workshop/index' => [
        ['src' => base_url . 'public/functions/contacts.js'],
        ['src' => base_url . 'public/functions/repair.js'],
        ['src' => base_url . 'public/functions/pieces.js'],
        ['src' => base_url . 'public/functions/services.js'],
    ],

    'warehouses' => [
        ['src' => base_url . 'public/functions/warehouses.js'],
    ],

    'categories' => [
        ['src' => base_url . 'public/functions/categories.js'],
    ],

    'taxes' => [
        ['src' => base_url . 'public/functions/taxes.js'],
    ],

    'contacts' => [
        ['src' => base_url . 'public/functions/contacts.js'],
    ],

    'reports/day' => [
        ['src' => base_url . 'public/functions/repair.js'],
        ['src' => base_url . 'public/functions/payments.js'],
        ['src' => base_url . 'public/functions/reports.js', 'type' => 'module'],
    ],

    'price_lists' => [
        ['src' => base_url . 'public/functions/price_lists.js'],
    ],
    'config' => [
        ['src' => base_url . 'public/functions/config.js', 'type' => 'module']
    ]
];

// ==================================
// Match de ruta
// ==================================
$matchedScripts = [];

foreach ($scriptsMap as $pattern => $scripts) {
    if (str_contains($uri, $pattern)) {
        $matchedScripts = $scripts;
        break;
    }
}

// ==================================
// Fallback
// ==================================
if (empty($matchedScripts)) {
    $matchedScripts = [
        ['src' => base_url . 'public/functions/positions.js'],
        ['src' => base_url . 'public/functions/offers.js'],
        ['src' => base_url . 'public/functions/bills.js'],
        ['src' => base_url . 'public/functions/payments.js'],
        ['src' => base_url . 'public/functions/reports.js', 'type' => 'module'],
        ['src' => base_url . 'public/functions/services.js'],
    ];
}

// ==================================
// Render final
// ==================================
foreach ($globalScripts as $script) {
    print_script($script);
}

foreach ($matchedScripts as $script) {
    print_script($script);
}
