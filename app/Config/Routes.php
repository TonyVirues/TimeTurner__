<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/login', 'AuthController::index');
$routes->get('/home', 'HomeController::index');
$routes->get('/pie', 'AuthController::pie');
$routes->get('/', 'HomeController::index');

// Rutas de horarios
$routes->get('horarios', 'HorariosController::index');
$routes->get('horarios/mostrar/(:num)', 'HorariosController::mostrar/$1');
$routes->post('horarios/crear', 'HorariosController::crear');
$routes->post('horarios/actualizar/(:num)', 'HorariosController::actualizar/$1');
$routes->post('horarios/eliminar/(:num)', 'HorariosController::eliminar/$1');
$routes->get('horarios/listado', 'HorariosController::listado');

// Rutas de turnos
$routes->get('turnos/mostrar/(:num)', 'TurnosController::mostrar/$1');
$routes->get('turnos/listado/horario/(:num)', 'TurnosController::listadoPorHorario/$1');
$routes->get('turnos/eventos', 'TurnosController::eventos');
$routes->post('turnos/crear', 'TurnosController::crear');
$routes->post('turnos/actualizar/(:num)', 'TurnosController::actualizar/$1');
$routes->post('turnos/eliminar/(:num)', 'TurnosController::eliminar/$1');
