<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', static function() {
    return redirect()->to('/login');
});

$routes->get('/dashboard', 'DashboardController::index', ['filter' => 'session']);

// Route Group Bidang / UPTD
$routes->group('bidang', ['filter' => 'session'], function($routes) {
    $routes->get('pengajuan', 'Bidang\PengajuanController::index');
    $routes->get('pengajuan/create', 'Bidang\PengajuanController::create');
    $routes->post('pengajuan/store', 'Bidang\PengajuanController::store');
    $routes->get('pengajuan/detail/(:num)', 'Bidang\PengajuanController::detail/$1');
    $routes->post('pengajuan/kirim/(:num)', 'Bidang\PengajuanController::kirim/$1');
    $routes->get('pengajuan/edit/(:num)', 'Bidang\PengajuanController::edit/$1');
    $routes->post('pengajuan/update/(:num)', 'Bidang\PengajuanController::update/$1');
});

// Route Group Admin OPD
$routes->group('opd', ['filter' => 'session'], function($routes) {
    $routes->get('verifikasi', 'Opd\VerifikasiController::index');
    $routes->get('verifikasi/detail/(:num)', 'Opd\VerifikasiController::detail/$1');
    $routes->post('verifikasi/aksi/(:num)', 'Opd\VerifikasiController::aksi/$1');
    
    $routes->get('konsolidasi', 'Opd\KonsolidasiController::index');
    $routes->get('konsolidasi/create', 'Opd\KonsolidasiController::create');
    $routes->post('konsolidasi/store', 'Opd\KonsolidasiController::store');
    $routes->get('konsolidasi/detail/(:num)', 'Opd\KonsolidasiController::detail/$1');
    $routes->get('rekomendasi/cetak/(:num)', 'Opd\KonsolidasiController::cetak/$1');
});

// Route Group Kepala DISKOMINFO
$routes->group('kominfo', ['filter' => 'session'], function($routes) {
    $routes->get('persetujuan', 'Kominfo\PersetujuanController::index');
    $routes->get('persetujuan/detail/(:num)', 'Kominfo\PersetujuanController::detail/$1');
    $routes->post('persetujuan/aksi/(:num)', 'Kominfo\PersetujuanController::aksi/$1');
});

// Route Group Super Admin
$routes->group('admin', ['filter' => 'session'], function($routes) {
    $routes->get('users', 'Admin\UserController::index');
    $routes->get('users/create', 'Admin\UserController::create');
    $routes->post('users/store', 'Admin\UserController::store');
    $routes->get('register-kadin', 'Admin\UserController::showRegisterKadinForm');
    $routes->post('register-kadin', 'Admin\UserController::processRegisterKadin');
});

$routes->get('register', '\App\Controllers\Auth\RegisterController::registerView');
$routes->post('register', '\App\Controllers\Auth\RegisterController::registerAction');

service('auth')->routes($routes);

