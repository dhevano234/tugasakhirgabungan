<?php

return [
    'resources' => [
        'patient' => [
            'singular' => 'Pasien',
            'plural' => 'Pasien',
            'navigation' => 'Data Pasien',
        ],
        'medical_record' => [
            'singular' => 'Rekam Medis',
            'plural' => 'Rekam Medis',
            'navigation' => 'Rekam Medis',
        ],
    ],
    'navigation' => [
        'patient' => 'Data Pasien',
        'medical_record' => 'Rekam Medis',
    ],
    'pages' => [
        'patient' => [
            'list' => 'Pasien',
            'create' => 'Tambah Pasien',
            'edit' => 'Ubah Pasien',
            'view' => 'Detail Pasien',
        ],
        'medical_record' => [
            'list' => 'Rekam Medis',
            'create' => 'Tambah Rekam Medis',
            'edit' => 'Ubah Rekam Medis',
            'view' => 'Detail Rekam Medis',
        ],
    ],
];
