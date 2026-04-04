<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/login', 'AuthController::index');
$routes->get('/home', 'Home::index');
$routes->get('/pie', 'AuthController::pie');
