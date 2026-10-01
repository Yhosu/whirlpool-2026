<?php

return [
    'available_nodes' => [
        'participant',
        'participant-coupon',
        'sale',
        'sale-item',
        'price',
        'invalid-participant',
    ],
    'paginate' => 100,

    'menus' => [
        'categoryId' => [
            'key' => 'name',
        ],
    ],
    'menu_items' => [
        'menuId' => [
            'key' => 'title',
        ],
    ],
    'per_page_front' => 6,

    'action_nodes' => [
        'participant' => ['enable_create' => true, 'enable_edit' => true, 'excel' => true],
        'participant-coupon' => ['enable_create' => false, 'enable_edit' => true, 'excel' => true],
        'sale' => ['enable_create' => false, 'enable_edit' => true, 'excel' => true],
        'invalid-participant' => ['enable_create' => false, 'enable_edit' => true, 'excel' => true],
    ],

    'menu' => [
        [
            'title' => 'Menú',
            'items' => [
                [
                    'title' => 'Participantes',
                    'url' => 'admin/node-list/participant',
                    'node' => 'participant',
                ],
                [
                    'title' => 'Cupones por participante',
                    'url' => 'admin/node-list/participant-coupon',
                    'node' => 'participant-coupon',
                ],
                [
                    'title' => 'Ventas',
                    'url' => 'admin/node-list/sale',
                    'node' => 'sale',
                ],
                [
                    'title' => 'Lista Negra',
                    'url' => 'admin/node-list/invalid-participant',
                    'node' => 'invalid-participant',
                ],
            ],
        ],
    ],

    'participant_coupons' => [
        'sale_id' => [
            'key' => 'name',
        ],
        'participant_id' => [
            'key' => 'name',
        ],
        'coupon_id' => [
            'key' => 'code',
        ],
        'price_id' => [
            'key' => 'name',
        ],
    ],
    'sales' => [
        'participant_id' => [
            'key' => 'name',
        ],
    ],
];
