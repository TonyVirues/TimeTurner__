<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Rutas login y registro
$routes->get('/', 'AuthController::login');
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::autenticar');
$routes->get('registro', 'AuthController::registro');
$routes->post('registro', 'AuthController::registrar');
$routes->get('logout', 'AuthController::logout');

// Rutas contenido principal
$routes->get('calendario', 'HomeController::calendario');
$routes->get('usuarios', 'HomeController::usuarios');
$routes->get('solicitudes', 'HomeController::solicitudes');

// Rutas de horarios
$routes->get('horarios/mostrar/(:num)', 'HorariosController::mostrar/$1');
$routes->post('horarios/crear', 'HorariosController::crear');
$routes->post('horarios/actualizar/(:num)', 'HorariosController::actualizar/$1');
$routes->post('horarios/eliminar/(:num)', 'HorariosController::eliminar/$1');
$routes->get('horarios/listado', 'HorariosController::listado');

// Rutas de turnos
$routes->get('turnos/mis-turnos', 'TurnosController::misTurnos');
$routes->get('turnos/mis-turnos-de/(:num)', 'TurnosController::misTurnosDe/$1');
$routes->get('turnos/mostrar/(:num)', 'TurnosController::mostrar/$1');
$routes->get('turnos/listado/horario/(:num)', 'TurnosController::listadoPorHorario/$1');
$routes->get('turnos/eventos', 'TurnosController::eventos');
$routes->post('turnos/crear', 'TurnosController::crear');
$routes->post('turnos/actualizar/(:num)', 'TurnosController::actualizar/$1');
$routes->post('turnos/eliminar/(:num)', 'TurnosController::eliminar/$1');

// Rutas de usuarios
$routes->get('usuarios/listado', 'UsuariosController::listado');
$routes->get('usuarios/mostrar/(:num)', 'UsuariosController::mostrar/$1');
$routes->post('usuarios/crear', 'UsuariosController::crear');
$routes->post('usuarios/actualizar/(:num)', 'UsuariosController::actualizar/$1');
$routes->post('usuarios/eliminar/(:num)', 'UsuariosController::eliminar/$1');
$routes->post('usuarios/liberar-y-eliminar/(:num)', 'UsuariosController::liberarYEliminar/$1');
$routes->get('usuarios/horarios-de-empleado/(:num)', 'UsuariosController::horariosDeEmpleado/$1');
$routes->post('usuarios/desactivar-con-liberar/(:num)', 'UsuariosController::desactivarConLiberar/$1');

// Rutas gestion de perfil
$routes->get('perfil', 'PerfilController::perfil');
$routes->post('perfil/actualizar', 'PerfilController::actualizar');

// Rutas solicitudes cambio de turno
$routes->get('solicitudes/mostrar/(:num)', 'SolicitudesCambioTurnoController::mostrar/$1');
$routes->get('solicitudes/listado', 'SolicitudesCambioTurnoController::listado');
$routes->post('solicitudes/crear', 'SolicitudesCambioTurnoController::crear');
$routes->post('solicitudes/aceptar/(:num)', 'SolicitudesCambioTurnoController::aceptar/$1');
$routes->post('solicitudes/rechazar/(:num)', 'SolicitudesCambioTurnoController::rechazar/$1');
$routes->post('solicitudes/cancelar/(:num)', 'SolicitudesCambioTurnoController::cancelar/$1');

// Rutas notificaciones solicitudes
$routes->get('solicitudes/contar-no-vistas', 'SolicitudesCambioTurnoController::contarNoVistas');
$routes->post('solicitudes/marcar-vistas', 'SolicitudesCambioTurnoController::marcarVistas');