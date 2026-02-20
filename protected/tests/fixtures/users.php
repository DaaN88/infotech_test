<?php
declare(strict_types=1);

return [
    'admin' => [
        'id' => 1,
        'username' => 'admin',
        'password_hash' => CPasswordHelper::hashPassword('admin'),
        'role' => 'admin',
        'created_at' => '2024-02-01 12:00:00',
        'updated_at' => '2024-02-01 12:00:00',
    ],
];
