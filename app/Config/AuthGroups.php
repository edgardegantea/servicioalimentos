<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{



    /*
    public string $defaultGroup = 'user';



    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Complete control of the site.',
        ],
        'admin' => [
            'title'       => 'Admin',
            'description' => 'Day to day administrators of the site.',
        ],
        'developer' => [
            'title'       => 'Developer',
            'description' => 'Site programmers.',
        ],
        'user' => [
            'title'       => 'User',
            'description' => 'General users of the site. Often customers.',
        ],
        'beta' => [
            'title'       => 'Beta User',
            'description' => 'Has access to beta-level features.',
        ],
    ];

    public array $permissions = [
        'admin.access'        => 'Can access the sites admin area',
        'admin.settings'      => 'Can access the main site settings',
        'users.manage-admins' => 'Can manage other admins',
        'users.create'        => 'Can create new non-admin users',
        'users.edit'          => 'Can edit existing non-admin users',
        'users.delete'        => 'Can delete existing non-admin users',
        'beta.access'         => 'Can access beta-level features',
    ];

    public array $matrix = [
        'superadmin' => [
            'admin.*',
            'users.*',
            'beta.*',
        ],
        'admin' => [
            'admin.access',
            'users.create',
            'users.edit',
            'users.delete',
            'beta.access',
        ],
        'developer' => [
            'admin.access',
            'admin.settings',
            'users.create',
            'users.edit',
            'beta.access',
        ],
        'user' => [],
        'beta' => [
            'beta.access',
        ],
    ];
*/



public array $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Acceso total al sistema.',
        ],
        'admin' => [
            'title'       => 'Gerente/Administrador',
            'description' => 'Gestiona inventario, cortes de caja y personal.',
        ],
        'cashier' => [
            'title'       => 'Cajero',
            'description' => 'Puede abrir/cerrar caja y cobrar cuentas.',
        ],
        'waiter' => [
            'title'       => 'Mesero',
            'description' => 'Toma pedidos y consulta estado de mesas.',
        ],
        'kitchen' => [
            'title'       => 'Cocina/Chef',
            'description' => 'Visualiza comandas (KDS) y cambia estados de platos.',
        ],
        'delivery' => [
            'title'       => 'Repartidor',
            'description' => 'Gestiona entregas a domicilio.',
        ],
        'customer' => [
            'title'       => 'Cliente',
            'description' => 'Usuario registrado para pedidos online.',
        ],
    ];

    // 2. Definimos Permisos Granulares (Ejemplos clave)
    public array $permissions = [
        'admin.access'        => 'Puede entrar al panel administrativo',
        'users.manage'        => 'Puede crear/editar/eliminar empleados',
        'pos.access'          => 'Puede acceder al punto de venta',
        'cash.manage'         => 'Puede realizar arqueos de caja',
        'inventory.edit'      => 'Puede ajustar stock y recetas',
        'reports.view'        => 'Puede ver reportes financieros',
        'orders.create'       => 'Puede crear nuevas comandas',
        'orders.cook'         => 'Puede cambiar estado de comanda a "Listo"',
    ];

    // 3. Matriz de Asignación (Qué rol tiene qué permiso)
    public array $matrix = [
        'superadmin' => ['*'], // Todo
        'admin'      => ['admin.access', 'users.manage', 'pos.access', 'cash.manage', 'inventory.edit', 'reports.view'],
        'cashier'    => ['pos.access', 'cash.manage', 'orders.create'],
        'waiter'     => ['pos.access', 'orders.create'], // El mesero vende, pero no toca la caja (generalmente)
        'kitchen'    => ['orders.cook'],
    ];



}
