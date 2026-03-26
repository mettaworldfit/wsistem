<?php
$role = $_SESSION['identity']->nombre_rol; // El rol del usuario (administrador, cajero, etc.)

// Definir las secciones comunes
$menu_sections = [
    'inicio' => [
        'label' => 'Inicio',
        'icon' => 'fas fa-home',
        'link' => base_url . 'home/index',
        'roles' => ['administrador', 'cajero'] // Ambos roles tienen acceso
    ],
    'ingresos' => [
        'label' => 'Ingresos',
        'drop_num' => 1,
        'icon' => 'fas fa-arrow-circle-down',
        'roles' => ['administrador', 'cajero'],
        'submenu' => [
            [
                'label' => 'Facturas de ventas',
                'link' => base_url . 'invoices/index',
                'icon' => 'fas fa-plus-circle',
                'icon_link' => base_url . 'invoices/addpurchase',
                'roles' => ['administrador', 'cajero'] // Ambos roles tienen acceso
            ],
            [
                'label' => 'Facturas de reparaciones',
                'link' => base_url . 'invoices/index_repair',
                'icon' => 'fas fa-plus-circle',
                'roles' => ['administrador'] // Ambos roles tienen acceso
            ],
            [
                'label' => 'Cotizaciones',
                'link' => base_url . 'invoices/quotes',
                'icon' => 'fas fa-plus-circle',
                'icon_link' => base_url . 'invoices/quote',
                'roles' => ['administrador', 'cajero'] // Ambos roles tienen acceso
            ],
            [
                'label' => 'Órdenes de ventas',
                'link' => base_url . 'invoices/orders',
                'icon' => 'fas fa-plus-circle',
                'roles' => ['administrador', 'cajero'] // Ambos roles tienen acceso
            ],
            [
                'label' => 'Órdenes de servicios',
                'link' => base_url . 'workshop/index',
                'icon' => 'fas fa-plus-circle',
                'roles' => ['administrador', 'cajero'] // Ambos roles tienen acceso
            ],
            [
                'label' => 'Pagos',
                'link' => base_url . 'payments/index',
                'icon' => 'fas fa-plus-circle',
                'icon_link' => base_url . 'payments/add',
                'roles' => ['administrador', 'cajero'] // Ambos roles tienen acceso
            ],
        ]
    ],
    'egresos' => [
        'label' => 'Egresos',
        'drop_num' => 2,
        'icon' => 'fas fa-arrow-circle-up',
        'roles' => ['administrador', 'cajero'],
        'submenu' => [
            [
                'label' => 'Gastos',
                'link' => base_url . 'bills/bills',
                'icon' => 'fas fa-plus-circle',
                'icon_link' => base_url . 'bills/addbills',
                'roles' => ['administrador', 'cajero']
            ]

        ]
    ],
    // 'taller' => [
    //     'label' => 'Taller',
    //     'drop_num' => 3,
    //     'icon' => 'fas fa-tools',
    //     'submenu' => [
    //         [
    //             'label' => 'Órdenes de servicios',
    //             'link' => base_url . 'workshop/index',
    //             'icon' => 'fas fa-plus-circle',
    //             'roles' => ['administrador', 'cajero'] // Ambos roles tienen acceso
    //         ]
    //     ]
    // ],
    'inventario' => [
        'label' => 'Inventario',
        'drop_num' => 3,
        'icon' => 'fas fa-box',
        'roles' => ['administrador', 'cajero'],
        'submenu' => [
            [
                'label' => 'Productos',
                'link' => base_url . 'products/index',
                'icon' => 'fas fa-plus-circle',
                'icon_link' => base_url . 'products/add',
                'roles' => ['administrador', 'cajero']
            ],
            [
                'label' => 'Piezas',
                'link' => base_url . 'pieces/index',
                'icon' => 'fas fa-plus-circle',
                'icon_link' => base_url . 'pieces/add',
                'roles' => ['administrador']
            ],
            [
                'label' => 'Servicios',
                'link' => base_url . 'services/index',
                'icon' => 'fas fa-plus-circle',
                'icon_link' => base_url . 'services/add',
                'roles' => ['administrador', 'cajero']
            ],
            [
                'label' => 'Almacenes',
                'link' => base_url . 'warehouses/index',
                'icon' => 'fas fa-plus-circle',
                'icon_link' => base_url . 'warehouses/add',
                'roles' => ['']
            ],
            [
                'label' => 'Categorías',
                'link' => base_url . 'categories/index',
                'icon' => 'fas fa-plus-circle',
                'icon_link' => base_url . 'categories/add',
                'roles' => ['administrador']
            ],
            [
                'label' => 'Listas de precios',
                'link' => base_url . 'price_lists/index',
                'icon' => 'fas fa-plus-circle',
                'icon_link' => base_url . 'price_lists/add',
                'roles' => ['administrador']
            ],
            [
                'label' => 'Marcas',
                'link' => base_url . 'brands/index',
                'icon' => 'fas fa-plus-circle',
                'icon_link' => base_url . 'brands/add',
                'roles' => ['administrador']
            ],
            [
                'label' => 'Impuestos',
                'link' => base_url . 'taxes/index',
                'icon' => 'fas fa-plus-circle',
                'icon_link' => base_url . 'taxes/add',
                'roles' => ['administrador']
            ],
            [
                'label' => 'Posiciones',
                'link' => base_url . 'positions/index',
                'icon' => 'fas fa-plus-circle',
                'icon_link' => base_url . 'positions/add',
                'roles' => ['administrador']
            ],
            [
                'label' => 'Ofertas',
                'link' => base_url . 'offers/index',
                'icon' => 'fas fa-plus-circle',
                'icon_link' => base_url . 'offers/add',
                'roles' => ['administrador']
            ],
        ]
    ],
    'contactos' => [
        'label' => 'Contactos',
        'drop_num' => 4,
        'icon' => 'fas fa-address-book',
        'roles' => ['administrador', 'cajero'],
        'submenu' => [
            [
                'label' => 'Clientes',
                'link' => base_url . 'contacts/customers',
                'icon' => 'fas fa-plus-circle',
                'icon_link' => base_url . 'contacts/add&type=1',
                'roles' => ['administrador', 'cajero']
            ],
            [
                'label' => 'Proveedores',
                'link' => base_url . 'contacts/providers',
                'icon' => 'fas fa-plus-circle',
                'icon_link' => base_url . 'contacts/add&type=0',
                'roles' => ['administrador', 'cajero']
            ],
        ]
    ],
    'reportes' => [
        'label' => 'Reportes',
        'drop_num' => 5,
        'icon' => 'fas fa-chart-bar',
        'roles' => ['administrador', 'cajero'],
        'submenu' => [
            [
                'label' => 'Ganancias por período',
                'link' => base_url . 'reports/earnings_period',
                'icon' => 'fas fa-search',
                'roles' => ['administrador']
            ],
            [
                'label' => 'Cierres de caja',
                'link' => base_url . 'reports/cash_closing',
                'icon' => 'fas fa-cash-register',
                'roles' => ['administrador', 'cajero']
            ],
            [
                'label' => 'Reportes de cantidades',
                'link' => base_url . 'reports/item_quantity',
                'icon' => 'fas fa-search',
                'roles' => ['administrador']
            ],
            [
                'label' => 'Reportes de inventario',
                'link' => base_url . 'reports/inventory',
                'icon' => 'fas fa-plus-circle',
                'roles' => ['administrador']
            ],
            [
                'label' => 'Reportes de ventas',
                'link' => base_url . 'reports/sales_period',
                'icon' => 'fas fa-search',
                'roles' => ['administrador']
            ],
            [
                'label' => 'Equipos vendidos',
                'link' => base_url . 'reports/equipment_sold',
                'icon' => 'fas fa-search',
                'roles' => ['administrador']
            ],
            [
                'label' => 'Ventas del día',
                'link' => base_url . 'reports/sales_today',
                'icon' => 'fas fa-chart-line',
                'roles' => ['administrador', 'cajero']
            ]
        ]
    ],
    'pos' => [
        'label' => 'POS',
        'icon' => 'fas fa-print',
        'link' => base_url . 'invoices/pos',
        'roles' => ['administrador']
    ],
];

$sidebar_footer = [
    'pos' => [
        'label' => 'POS',
        'icon' => 'fas fa-print',
        'link' => base_url . 'invoices/pos',
        'roles' => ['cajero']
    ],
    'usuarios' => [
        'label' => 'Usuarios',
        'icon' => 'fas fa-users',
        'link' => base_url . 'users/index',
        'roles' => ['administrador']
    ],
    [
        'label' => 'Configuración',
        'icon' => 'fas fa-cog',
        'link' => base_url . 'config/index',
        'roles' => ['administrador']
    ],
];

// Función para mostrar un elemento del menú
function display_menu_item($item, $footer = false)
{
    $role = $_SESSION['identity']->nombre_rol;

    // ===== FOOTER =====
    if ($footer) {

        // LINKS

        if (in_array($role, $item['roles'])) {

            echo '<li>';
            echo '<div class="link">';
            echo '<a href="' . $item['link'] . '" class="full-link">';
            echo '<i class="mr-3 ' . $item['icon'] . '"></i>' . $item['label'];
            echo '</a>';
            echo '</div>';
            echo '</li>';
        }

        return;
    }

    // ===== MENU NORMAL =====
    $roles = isset($item['roles']) ? $item['roles'] : ['administrador'];

    if (!in_array($role, $roles)) return;

    if (isset($item['submenu'])) {

        echo '<li class="dropdown-' . $item['drop_num'] . '">';
        echo '<div class="link"><i class="mr-3 ' . $item['icon'] . '"></i>' . $item['label'] . ' <i class="fas fa-chevron-down"></i></div>';
        echo '<ul class="submenu">';

        foreach ($item['submenu'] as $submenu) {

            if (in_array($role, $submenu['roles'])) {

                echo '<li class="page">';
                echo '<a href="' . $submenu['link'] . '">' . $submenu['label'] . '</a>';

                if (isset($submenu['icon_link'])) {
                    echo '<a href="' . $submenu['icon_link'] . '"><i class="' . $submenu['icon'] . '"></i></a>';
                }

                echo '</li>';
            }
        }

        echo '</ul>';
        echo '</li>';
    } else {

        echo '<li>';
        echo '<div class="link">
                <a href="' . $item['link'] . '" class="full-link">
                    <i class="mr-3 ' . $item['icon'] . '"></i>' . $item['label'] . '
                </a>
              </div>';
        echo '</li>';
    }
}
