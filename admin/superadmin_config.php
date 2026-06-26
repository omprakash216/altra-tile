<?php
return [
    // Hardcoded superadmin (not stored in database).
    'superadmin_credentials' => [
        'username' => 'abcdefgh79@gmail.com',
        'password' => 'admin@12345',
    ],

    // If tbl_admin has no role column, these usernames will be treated as superadmin.
    'fallback_superadmin_usernames' => [],

    // Keep backups outside publicly browsable folders as much as possible.
    'backup_directory' => __DIR__ . '/../private_backups/system',

    // Old backups above this count will be deleted automatically.
    'max_backups' => 25,

    // true = backup/reset/restore all base tables in current selected DB.
    'include_all_tables' => true,

    // Optional table blacklist when include_all_tables is true.
    'excluded_tables' => [],

    // These folders are copied in backup and cleared/restored during reset/restore.
    'upload_directories' => [
        'uploads/banners',
        'uploads/event_banners',
    ],
];
