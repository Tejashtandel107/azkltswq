<?php

return [
    'APP_NAME_SHORT' => env('APP_NAME_SHORT', 'GFP'),
    'PAGINATION' => 100,
    'DB_DATE_FORMAT' => 'Y-m-d h:i A',
    'DATE_FORMAT' => 'd/m/Y h:i A',
    'DATE_FORMAT_SHORT' => 'd/m/Y',
    'DEFAULT_IMG_HOLDER' => '/assets/app/images/place-holder.svg',
    'DEFAULT_IMG_PROFILE' => '/assets/web/images/default-avatar.png',
    'status' => [
        'active' => 1,
        'inactive' => 0,
    ],
    'default' => [
        'enabled' => 1,
        'disabled' => 0,
    ],
    'database' => [
        'BACKUP_PATH' => 'db-backup',
    ],
    'USER_ROLE_LABELS' => [
        1 => ['role' => 'superadmin', 'display_name' => 'Super Admin', 'color' => 'primary'],
        2 => ['role' => 'admin', 'display_name' => 'Admin', 'color' => 'success'],
        3 => ['role' => 'user', 'display_name' => 'User', 'color' => 'warning'],
    ],
    'ROLE_SUPER_ADMIN' => 'superadmin',
    'ROLE_ADMIN' => 'admin',
    'ROLE_USER' => 'user',
    'ROLE_SUPER_ADMIN_ID' => 1,
    'ROLE_ADMIN_ID' => 2,
    'ROLE_USER_ID' => 3,
    'USER_PROFILE_PATH' => 'profile',
    'COMPANY_LOGO_PATH' => 'logo',
    'CUSTOMER_PROFILE_PATH' => 'customer/profile',
    'TAGLINE' => 'Shree Sadguru Dev Ki Jay',
    'SETTINGS_KEY' => 'comapny_info',
    'ROLE_SUPER_ADMIN_ID' => 1,
    'ROLE_ADMIN_ID' => 2,
    'ROLE_USER_ID' => 3,
    'CUSTOMER_ORDER_TYPE' => [
        'INWARD' => 'inward',
        'OUTWARD' => 'outward',
    ],
    'TOTAL_CHAMBERS' => 4,
    'TOTAL_FLOORS' => 2,
    'TOTAL_GRIDS' => 20,
    'TEMP_FOLDER_PATH' => 'temp/',
];
