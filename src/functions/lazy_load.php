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
function print_script($asset)
{
    $src = versioned_js($asset['src']);

    $extension = strtolower(pathinfo($asset['src'], PATHINFO_EXTENSION));

    if ($extension === 'css') {
        echo '<link rel="stylesheet" href="' . $src . '">' . PHP_EOL;
        return;
    }

    $type = $asset['type'] ?? 'text/javascript';

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
        'src'  => base_url . 'src/modules/users/users.services.js',
        'type' => 'text/javascript'
    ],
    [
        'src'  => base_url . 'src/modules/invoices/invoices.services.js',
        'type' => 'module'
    ],
    [
        'src'  => base_url . 'src/modules/workshop/workshop.services.js',
        'type' => 'text/javascript'
    ]
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
            'src'  => base_url . 'src/modules/home/home.services.js',
            'type' => 'module'
        ],
        [
            'src'  => base_url . 'src/modules/reports/reports.services.js',
            'type' => 'module'
        ]
    ],

    'invoices/addpurchase' => [
        ['src' => base_url . 'src/modules/pieces/pieces.services.js',],
        ['src' => base_url . 'src/modules/products/products.services.js'],
        ['src' => base_url . 'src/modules/services/services.services.js'],
        ['src' => base_url . 'src/modules/contacts/contacts.services.js'],
        ['src' => base_url . 'src/modules/price_lists/price_lists.services.js'],
    ],

    'invoices/edit' => [
        ['src' => base_url . 'src/modules/pieces/pieces.services.js'],
        ['src' => base_url . 'src/modules/products/products.services.js'],
        ['src' => base_url . 'src/modules/services/services.services.js'],
        ['src' => base_url . 'src/modules/contacts/contacts.services.js'],
        ['src' => base_url . 'src/modules/price_lists/price_lists.services.js'],
    ],

    'invoices/quote' => [
        ['src' => base_url . 'src/modules/pieces/pieces.services.js'],
        ['src' => base_url . 'src/modules/products/products.services.js'],
        ['src' => base_url . 'src/modules/services/services.services.js'],
        ['src' => base_url . 'src/modules/contacts/contacts.services.js'],
        ['src' => base_url . 'src/modules/price_lists/price_lists.services.js'],
    ],

    'invoices/add_order' => [
        ['src' => base_url . 'src/modules/pieces/pieces.services.js'],
        ['src' => base_url . 'src/modules/products/products.services.js'],
        ['src' => base_url . 'src/modules/services/services.services.js'],
        ['src' => base_url . 'src/modules/contacts/contacts.services.js'],
        ['src' => base_url . 'src/modules/price_lists/price_lists.services.js'],
    ],

    'invoices/pos' => [
        ['src' => base_url . 'src/modules/reports/reports.services.js', 'type' => 'module'],
        ['src' => base_url . 'src/modules/invoices/pos.services.js', 'type' => 'module'],
        ['src'  => base_url . 'src/modules/invoices/style.css', 'type' => 'text/css'],
        ['src' => base_url . 'src/modules/pieces/pieces.services.js'],
        ['src' => base_url . 'src/modules/products/products.services.js'],
        ['src' => base_url . 'src/modules/services/services.services.js'],
        ['src' => base_url . 'src/modules/contacts/contacts.services.js'],
        ['src' => base_url . 'src/modules/price_lists/price_lists.services.js'],
    ],

    'invoices/repair_edit' => [
        ['src' => base_url . 'src/modules/pieces/pieces.services.js'],
        ['src' => base_url . 'src/modules/services/services.services.js'],
        ['src' => base_url . 'src/modules/contacts/contacts.services.js'],
        ['src' => base_url . 'src/modules/repair/repair.services.js'],
        ['src' => base_url . 'src/modules/price_lists/price_lists.services.js'],
    ],

    'invoices/index' => [
        ['src' => base_url . 'src/modules/price_lists/price_lists.services.js'],
        ['src' => base_url . 'src/modules/repair/repair.services.js'],
        ['src' => base_url . 'src/modules/contacts/contacts.services.js'],
        ['src' => base_url . 'src/modules/services/services.services.js'],
        ['src' => base_url . 'src/modules/pieces/pieces.services.js'],
    ],

    'invoices/addrepair' => [
        ['src' => base_url . 'src/modules/price_lists/price_lists.services.js'],
        ['src' => base_url . 'src/modules/repair/repair.services.js'],
        ['src' => base_url . 'src/modules/contacts/contacts.services.js'],
        ['src' => base_url . 'src/modules/services/services.services.js'],
        ['src' => base_url . 'src/modules/pieces/pieces.services.js'],
    ],

    'invoices/index_repair' => [
        ['src' => base_url . 'src/modules/price_lists/price_lists.services.js'],
        ['src' => base_url . 'src/modules/repair/repair.services.js'],
        ['src' => base_url . 'src/modules/contacts/contacts.services.js'],
        ['src' => base_url . 'src/modules/services/services.services.js'],
        ['src' => base_url . 'src/modules/pieces/pieces.services.js'],
    ],

    'invoices/orders' => [
        ['src' => base_url . 'src/modules/price_lists/price_lists.services.js'],
        ['src' => base_url . 'src/modules/repair/repair.services.js'],
        ['src' => base_url . 'src/modules/contacts/contacts.services.js'],
        ['src' => base_url . 'src/modules/services/services.services.js'],
        ['src' => base_url . 'src/modules/pieces/pieces.services.js'],
    ],

    'products/index' => [
        ['src' => base_url . 'src/modules/products/products.services.js'],
        ['src' => base_url . 'src/modules/price_lists/price_lists.services.js'],
    ],

    'products' => [
        ['src' => base_url . 'src/modules/products/products.services.js'],
        ['src' => base_url . 'src/modules/price_lists/price_lists.services.js'],
    ],

    'pieces/index' => [
        ['src' => base_url . 'src/modules/pieces/pieces.services.js'],
        ['src' => base_url . 'src/modules/price_lists/price_lists.services.js'],
    ],

    'pieces' => [
        ['src' => base_url . 'src/modules/pieces/pieces.services.js'],
        ['src' => base_url . 'src/modules/price_lists/price_lists.services.js'],
    ],

    'workshop/index' => [
        ['src' => base_url . 'src/modules/contacts/contacts.services.js'],
        ['src' => base_url . 'src/modules/repair/repair.services.js'],
        ['src' => base_url . 'src/modules/pieces/pieces.services.js'],
        ['src' => base_url . 'src/modules/services/services.services.js'],
    ],

    'warehouses' => [
        ['src' => base_url . 'src/modules/warehouses/warehouses.services.js'],
    ],

    'bills' => [
        ['src' => base_url . 'src/modules/bills/bills.services.js', 'type' => 'module'],
    ],

    'categories' => [
        ['src' => base_url . 'src/modules/categories/categories.services.js'],
    ],

    'taxes' => [
        ['src' => base_url . 'src/modules/taxes/taxes.services.js'],
    ],

    'contacts' => [
        ['src' => base_url . 'src/modules/contacts/contacts.services.js'],
    ],

    'reports/day' => [
        ['src' => base_url . 'src/modules/repair/repair.services.js'],
        ['src' => base_url . 'src/modules/payments/payments.services.js'],
        ['src' => base_url . 'src/modules/reports/reports.services.js', 'type' => 'module'],
    ],
    'reports' => [
        ['src'  => base_url . 'src/modules/reports/style.css', 'type' => 'text/css']
    ],
    'price_lists' => [
        ['src' => base_url . 'src/modules/price_lists/price_lists.services.js'],
    ],
    'config' => [
        ['src' => base_url . 'src/modules/config/config.services.js', 'type' => 'module']
    ],
    'ecf' => [
       ['src'  => base_url . 'src/modules/ecf/ecf.services.js', 'type' => 'text/javascript'],
       ['src'  => base_url . 'src/modules/ecf/style.css', 'type' => 'text/css']
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
        ['src' => base_url . 'src/modules/positions/positions.services.js'],
        ['src' => base_url . 'src/modules/offers/offers.services.js'],
        ['src' => base_url . 'src/modules/payments/payments.services.js'],
        ['src' => base_url . 'src/modules/reports/reports.services.js', 'type' => 'module'],
        ['src' => base_url . 'src/modules/services/services.services.js'],
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
