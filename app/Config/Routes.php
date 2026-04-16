<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Rutas Login , registro
$routes->get('/login', 'AuthController::login');
$routes->get('/registro', 'AuthController::registro');

// Rutas Contenido principal
$routes->get('/', 'HomeController::calendario'); //@mar Esto está temporal, porque quiero que se abra calendario del tiron
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
