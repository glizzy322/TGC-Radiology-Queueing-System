<?php

return [
    'brand' => [
        'title' => 'Radiology Department',
        'alt' => 'Tagum Global Medical Center Logo',
        'logo' => 'images/logo.png',
    ],
    'time' => date('h:i:s A'),
    'announcement' => [
        'slides' => [
            '/images/slide1.jpg',
            '/images/slide2.jpg',
            '/images/slide3.jpg',
            '/images/slide4.jpg',
        ],
    ],
    'serving' => [
        'title' => 'Now Serving',
        'ipd' => [
            ['id' => 'XR004', 'codeClass' => 'XR'],
            ['id' => 'UT002', 'codeClass' => 'UT'],
        ],
        'opd' => [
            ['id' => 'CT001', 'codeClass' => 'CT'],
        ],
    ],
    'queues' => [
        [
            'title' => 'X-RAY',
            'count' => 15,
            'items' => [
                ['id' => 'XR005', 'type' => 'IPD'],
                ['id' => 'XR006', 'type' => 'IPD'],
                ['id' => 'XR007', 'type' => 'IPD'],
                ['id' => 'XR008', 'type' => 'IPD'],
                ['id' => 'XR009', 'type' => 'OPD'],
                ['id' => 'XR010', 'type' => 'OPD'],
                ['id' => 'XR011', 'type' => 'OPD'],
                ['id' => 'XR012', 'type' => 'OPD'],
                ['id' => 'XR013', 'type' => 'OPD'],
                ['id' => 'XR014', 'type' => 'OPD'],
                ['id' => 'XR015', 'type' => 'OPD'],
                ['id' => 'XR016', 'type' => 'OPD'],
                ['id' => 'XR017', 'type' => 'OPD'],
                ['id' => 'XR018', 'type' => 'OPD'],
                ['id' => 'XR019', 'type' => 'OPD'],
            ],
        ],
        [
            'title' => 'Ultrasound',
            'count' => 8,
            'items' => [
                ['id' => 'UT003', 'type' => 'IPD'],
                ['id' => 'UT004', 'type' => 'IPD'],
                ['id' => 'UT005', 'type' => 'OPD'],
                ['id' => 'UT006', 'type' => 'OPD'],
                ['id' => 'UT007', 'type' => 'OPD'],
                ['id' => 'UT008', 'type' => 'OPD'],
                ['id' => 'UT009', 'type' => 'OPD'],
                ['id' => 'UT010', 'type' => 'OPD'],
            ],
        ],
        [
            'title' => 'CT-Scan',
            'count' => 12,
            'items' => [
                ['id' => 'CT002', 'type' => 'IPD'],
                ['id' => 'CT003', 'type' => 'IPD'],
                ['id' => 'CT004', 'type' => 'IPD'],
                ['id' => 'CT005', 'type' => 'OPD'],
                ['id' => 'CT006', 'type' => 'OPD'],
                ['id' => 'CT007', 'type' => 'OPD'],
                ['id' => 'CT008', 'type' => 'OPD'],
                ['id' => 'CT009', 'type' => 'OPD'],
                ['id' => 'CT010', 'type' => 'OPD'],
                ['id' => 'CT011', 'type' => 'OPD'],
                ['id' => 'CT012', 'type' => 'OPD'],
                ['id' => 'CT013', 'type' => 'OPD'],
            ],
        ],
    ],
];
