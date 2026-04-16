<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Rutas Login , registro
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::autenticar');
$routes->get('/logout', 'AuthController::logout');
$routes->get('/registro', 'AuthController::registro');
$routes->post('/registro', 'AuthController::registrar');

// Rutas Contenido principal
$routes->get('/', 'AuthController::login');
$routes->get('/calendario', 'HomeController::calendario');

// Rutas de horarios
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

// Rutas de usuarios
$routes->get('usuarios/listado', 'UsuariosController::listado');
