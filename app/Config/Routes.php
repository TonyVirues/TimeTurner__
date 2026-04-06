<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Rutas de horarios
$routes->get('horarios', 'Horarios::index');
$routes->get('horarios/eventos', 'Horarios::eventos');
$routes->get('horarios/mostrar/(:num)', 'Horarios::mostrar/$1');
$routes->post('horarios/crear', 'Horarios::crear');
$routes->post('horarios/actualizar/(:num)', 'Horarios::actualizar/$1');
$routes->post('horarios/eliminar/(:num)', 'Horarios::eliminar/$1');
$routes->get('horarios/listado', 'Horarios::listado');
