<?php
return [
    'auctions' => [
        'name' => 'div.mt-1',
        'uri' => 'a.btn',
        'start_date' => 'div.ml-1',
        'end_date' => '', //Are not available on web-site at the moment. Included for better extension.
        'city' => 'span.text-prim',
        'lots_number' => 'span.text-graynorm'
    ],
    'car' => [
        'characteristics' => [
            'anchor' => [
                'tag' => 'h3.mb-2',
                'text' => 'Characteristics'
            ],
        ],
        'description' => [
            'anchor' => [
                'tag' => 'h3.mb-3',
                'text' => 'Options'
            ],
        ],
        'inspection_report_url' => [
            'anchor' => [
                'tag' => 'a.btn',
                'text' => 'View the technical inspection'
            ],
        ],
        'lot_number' => 'h3.h4',
        'lot_price' => 'h4.d-table-cell span.text-nowrap',
        'images' => 'a[data-gallery="pictures"]'
    ],
];
