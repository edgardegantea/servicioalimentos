<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------------------------------------------------------------------
// 1. Rutas de Autenticación (Shield)
// --------------------------------------------------------------------
// Esto habilita /login, /register, /logout automáticamente
service('auth')->routes($routes);

// --------------------------------------------------------------------
// 2. Ruta Pública (Landing Page / Carta Digital)
// --------------------------------------------------------------------
$routes->get('/', 'Home::index'); // Página principal
$routes->get('menu', 'Web\Menu::index'); // Carta pública para clientes

// --------------------------------------------------------------------
// 3. Área Administrativa (Backoffice)
// --------------------------------------------------------------------
// Solo SuperAdmin y Admin pueden entrar aquí
$routes->group('admin', ['filter' => 'group:superadmin,admin', 'namespace' => 'App\Controllers\Admin'], function($routes) {
    
    // Dashboard Principal
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');

    // Gestión de Empleados
    $routes->resource('staff', ['controller' => 'StaffController']);

    // Gestión de Menú e Inventario
    $routes->resource('categories');
    $routes->resource('products');
    
    // Gestión de Mesas
    $routes->resource('tables');

    // Reportes Financieros
    $routes->get('reports/sales', 'Reports::sales');
    $routes->get('reports/inventory', 'Reports::inventory');
});

// --------------------------------------------------------------------
// 4. Área Operativa (Punto de Venta - POS)
// --------------------------------------------------------------------
// Cajeros, Meseros y Admins tienen acceso
$routes->group('pos', ['filter' => 'group:superadmin,admin,cashier,waiter', 'namespace' => 'App\Controllers\Pos'], function($routes) {
    
    $routes->get('/', 'Terminal::index'); // La pantalla de venta táctil
    
    $routes->get('register/open', 'CashRegister::open', ['filter' => 'group:superadmin,admin,cashier']);
    $routes->post('register/open', 'CashRegister::processOpen', ['filter' => 'group:superadmin,admin,cashier']);
    $routes->get('register/close', 'CashRegister::close', ['filter' => 'group:superadmin,admin,cashier']);
    
    $routes->post('cart/add', 'Cart::addItem');
    $routes->post('cart/remove', 'Cart::removeItem');
    $routes->post('checkout', 'Checkout::process');


    $routes->get('ready', 'DeliveryMonitor::index');
    $routes->post('ready/(:num)/deliver', 'DeliveryMonitor::markDelivered/$1');

    $routes->get('tables', 'Tables::index');
    
    $routes->get('payment/(:num)', 'Payment::index/$1');

    $routes->get('tables/occupy/(:num)', 'Tables::occupy/$1');

    $routes->get('payment/(:num)', 'Payment::index/$1');      // Ver Pre-cuenta
    $routes->post('payment/(:num)/pay', 'Payment::pay/$1');   // Procesar cobro (AJAX)

    $routes->get('register/open', 'CashRegister::open');
    $routes->post('register/open', 'CashRegister::processOpen');
    $routes->get('register/close', 'CashRegister::close');
    $routes->post('register/close', 'CashRegister::processClose');

});

// --------------------------------------------------------------------
// 5. Área de Cocina (KDS - Kitchen Display System)
// --------------------------------------------------------------------
// Solo Personal de Cocina y Admins
$routes->group('kitchen', ['filter' => 'group:superadmin,admin,kitchen', 'namespace' => 'App\Controllers\Kitchen'], function($routes) {
    
    // $routes->get('monitor', 'Monitor::index'); // Pantalla de comandas en vivo
    // $routes->post('order/(:num)/ready', 'Monitor::markAsReady/$1'); // Marcar plato listo

    $routes->get('monitor', 'Monitor::index'); 
    $routes->post('order/(:num)/status', 'Monitor::updateStatus/$1');

});

// --------------------------------------------------------------------
// 6. API Interna (Para llamadas AJAX desde el Frontend)
// --------------------------------------------------------------------
$routes->group('api', ['namespace' => 'App\Controllers\Api', 'filter' => 'session'], function($routes) {
    $routes->get('products/search', 'Products::search'); // Buscador predictivo
    $routes->get('notifications', 'Notifications::poll'); // Polling de nuevas órdenes
});