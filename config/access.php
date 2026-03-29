<?php

$resourcePermissions = static fn (string $resource): array => [
    "view_{$resource}",
    "view_any_{$resource}",
    "create_{$resource}",
    "update_{$resource}",
    "restore_{$resource}",
    "restore_any_{$resource}",
    "replicate_{$resource}",
    "reorder_{$resource}",
    "delete_{$resource}",
    "delete_any_{$resource}",
    "force_delete_{$resource}",
    "force_delete_any_{$resource}",
];

return [
    'roles' => [
        [
            'name' => 'super_admin',
            'guard_name' => 'web',
            'permissions' => array_merge(
                $resourcePermissions('product'),
                $resourcePermissions('product::category'),
                $resourcePermissions('product::supplier'),
                $resourcePermissions('order'),
                $resourcePermissions('client'),
                $resourcePermissions('user'),
                ['page_Pos']
            ),
        ],
    ],

    'direct_permissions' => [],
];
