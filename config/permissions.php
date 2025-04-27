<?php

return [
    'roles' => [
        'admin' => [
            'view_users',
            'edit_users',
            'update_user',
            'delete_user',
            'manage_permissions',
            'view_patients',
            'manage_patients',
            'view_appointments',
            'manage_appointments',
            'view_medical_records',
            'manage_medical_records'
        ],
        'doctor' => [
            'view_users',
            'view_patients',
            'manage_patients',
            'view_appointments',
            'manage_appointments',
            'view_medical_records',
            'manage_medical_records'
        ],
        'patient' => [
            'view_users',
            'view_appointments',
            'manage_appointments',
            'view_medical_records'
        ],
        'customer' => [
            'view_users',
            'view_appointments',
            'manage_appointments'
        ]
    ]
];