<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/home', 'Home::index');
$routes->get('/login', 'AuthController::login');
$routes->get('/registro', 'AuthController::registro');

// Rutas de horarios
$routes->get('horarios', 'Horarios::index');
$routes->get('horarios/mostrar/(:num)', 'Horarios::mostrar/$1');
$routes->post('horarios/crear', 'Horarios::crear');
$routes->post('horarios/actualizar/(:num)', 'Horarios::actualizar/$1');
$routes->post('horarios/eliminar/(:num)', 'Horarios::eliminar/$1');
$routes->get('horarios/listado', 'Horarios::listado');

// Rutas de turnos
$routes->get('turnos/mostrar/(:num)', 'Turnos::mostrar/$1');
$routes->get('turnos/listado/horario/(:num)', 'Turnos::listadoPorHorario/$1');
$routes->get('turnos/eventos', 'Turnos::eventos');
$routes->post('turnos/crear', 'Turnos::crear');
$routes->post('turnos/actualizar/(:num)', 'Turnos::actualizar/$1');
$routes->post('turnos/eliminar/(:num)', 'Turnos::eliminar/$1');
