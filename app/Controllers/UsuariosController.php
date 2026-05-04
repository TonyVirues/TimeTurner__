<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\TurnoModel;
use App\Models\SolicitudCambioTurnoModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class UsuariosController extends BaseController
{
  protected UsuarioModel $usuarioModel;
  protected TurnoModel $turnoModel;
  protected SolicitudCambioTurnoModel $solicitudModel;
  protected $db;

  public function initController(
    RequestInterface $request,
    ResponseInterface $response,
    LoggerInterface $logger
  ) {
    parent::initController($request, $response, $logger);
    $this->usuarioModel = new UsuarioModel();
    $this->turnoModel = new TurnoModel();
    $this->solicitudModel = new SolicitudCambioTurnoModel();
    $this->db = \Config\Database::connect();
  }

  /**
   * Devuelve todos los usuarios de la empresa del administrador logueado
   * @return ResponseInterface
   */
  public function listado(): ResponseInterface
  {
    $errorPermisos = $this->exigirAdministrador();

    if ($errorPermisos !== null) {
      return $errorPermisos;
    }

    $idEmpresa = (int) session()->get('usu_id_empresa');
    $usuarios = $this->usuarioModel->getUsuariosPorEmpresa($idEmpresa);

    foreach ($usuarios as &$usuario) {
      unset($usuario['usu_password']);
    }

    return $this->response->setJSON([
      'status' => 'success',
      'data' => $usuarios,
    ]);
  }

  /**
   * Devuelve un usuario concreto de la empresa del administrador logueado
   * @param int $idUsuario
   * @return ResponseInterface
   */
  public function mostrar(int $idUsuario): ResponseInterface
  {
    $errorPermisos = $this->exigirAdministrador();

    if ($errorPermisos !== null) {
      return $errorPermisos;
    }

    $usuario = $this->usuarioModel->getUsuarioPorId($idUsuario);

    if (!$usuario) {
      return $this->responderNoEncontrado();
    }

    $errorEmpresa = $this->validarUsuarioPerteneceAEmpresaActual($usuario);

    if ($errorEmpresa !== null) {
      return $errorEmpresa;
    }

    unset($usuario['usu_password']);

    return $this->response->setJSON([
      'status' => 'success',
      'data' => $usuario,
    ]);
  }

  /**
   * Crea un nuevo usuario dentro de la empresa del administrador logueado
   * @return ResponseInterface
   */
  public function crear(): ResponseInterface
  {
    $errorPermisos = $this->exigirAdministrador();

    if ($errorPermisos !== null) {
      return $errorPermisos;
    }

    if (!$this->validate($this->obtenerReglasUsuario(true))) {
      return $this->responderErrorValidacion();
    }

    $datos = $this->request->getPost();

    if ($this->usuarioModel->existeEmail($datos['usu_email'])) {
      return $this->response->setStatusCode(400)->setJSON([
        'status' => 'error',
        'message' => 'Ya existe un usuario con ese email.',
      ]);
    }

    $datosInsertar = [
      'usu_id_empresa' => (int) session()->get('usu_id_empresa'),
      'usu_tipo_cuenta' => 'empresa',
      'usu_nombre' => $datos['usu_nombre'],
      'usu_apellidos' => $datos['usu_apellidos'],
      'usu_email' => $datos['usu_email'],
      'usu_password' => $datos['usu_password'],
      'usu_rol' => $datos['usu_rol'],
      'usu_activo' => isset($datos['usu_activo']) ? (int) $datos['usu_activo'] : 1,
    ];

    $resultado = $this->usuarioModel->insert($datosInsertar);

    if (!$resultado) {
      return $this->response->setStatusCode(500)->setJSON([
        'status' => 'error',
        'message' => 'No se pudo crear el usuario.',
      ]);
    }

    $usuarioCreado = $this->usuarioModel->getUsuarioPorId((int) $resultado);

    if ($usuarioCreado) {
      unset($usuarioCreado['usu_password']);
    }

    return $this->response->setStatusCode(201)->setJSON([
      'status' => 'success',
      'message' => 'Usuario creado correctamente.',
      'data' => $usuarioCreado,
    ]);
  }

  /**
   * Actualiza un usuario existente de la empresa del administrador logueado
   * @param int $idUsuario
   * @return ResponseInterface
   */
  public function actualizar(int $idUsuario): ResponseInterface
  {
    $errorPermisos = $this->exigirAdministrador();

    if ($errorPermisos !== null) {
      return $errorPermisos;
    }

    $usuario = $this->usuarioModel->getUsuarioPorId($idUsuario);

    if (!$usuario) {
      return $this->responderNoEncontrado();
    }

    $errorEmpresa = $this->validarUsuarioPerteneceAEmpresaActual($usuario);

    if ($errorEmpresa !== null) {
      return $errorEmpresa;
    }

    $datos = $this->request->getPost();
    $soloActivo = count($datos) === 1 && array_key_exists('usu_activo', $datos);

    if (!$soloActivo && !$this->validate($this->obtenerReglasUsuario(false))) {
      return $this->responderErrorValidacion();
    }

    $datosActualizar = [];

    if (array_key_exists('usu_nombre', $datos)) {
      $datosActualizar['usu_nombre'] = $datos['usu_nombre'];
    }

    if (array_key_exists('usu_apellidos', $datos)) {
      $datosActualizar['usu_apellidos'] = $datos['usu_apellidos'];
    }

    if (array_key_exists('usu_email', $datos)) {
      if (
        $datos['usu_email'] !== $usuario['usu_email'] &&
        $this->usuarioModel->existeEmail($datos['usu_email'])
      ) {
        return $this->response->setStatusCode(400)->setJSON([
          'status' => 'error',
          'message' => 'Ya existe otro usuario con ese email.',
        ]);
      }

      $datosActualizar['usu_email'] = $datos['usu_email'];
    }

    if (!empty($datos['usu_password'])) {
      $datosActualizar['usu_password'] = $datos['usu_password'];
    }

    if (array_key_exists('usu_rol', $datos)) {
      $datosActualizar['usu_rol'] = $datos['usu_rol'];
    }

    if (array_key_exists('usu_activo', $datos)) {
      $datosActualizar['usu_activo'] = (int) $datos['usu_activo'];
    }

    if (empty($datosActualizar)) {
      return $this->response->setStatusCode(400)->setJSON([
        'status' => 'error',
        'message' => 'No se han enviado datos para actualizar.',
      ]);
    }

    $resultado = $this->usuarioModel->update($idUsuario, $datosActualizar);

    if (!$resultado) {
      return $this->response->setStatusCode(500)->setJSON([
        'status' => 'error',
        'message' => 'No se pudo actualizar el usuario.',
      ]);
    }

    $usuarioActualizado = $this->usuarioModel->getUsuarioPorId($idUsuario);

    if ($usuarioActualizado) {
      unset($usuarioActualizado['usu_password']);
    }

    return $this->response->setJSON([
      'status' => 'success',
      'message' => 'Usuario actualizado correctamente.',
      'data' => $usuarioActualizado,
    ]);
  }

  /**
   * Elimina un usuario de la empresa del administrador logueado
   * @param int $idUsuario
   * @return ResponseInterface
   */
  public function eliminar(int $idUsuario): ResponseInterface
  {
    $errorPermisos = $this->exigirAdministrador();

    if ($errorPermisos !== null) {
      return $errorPermisos;
    }

    $usuario = $this->usuarioModel->getUsuarioPorId($idUsuario);

    if (!$usuario) {
      return $this->responderNoEncontrado();
    }

    $errorEmpresa = $this->validarUsuarioPerteneceAEmpresaActual($usuario);

    if ($errorEmpresa !== null) {
      return $errorEmpresa;
    }

    $tieneTurnos = count($this->turnoModel->getTurnosPorUsuario($idUsuario)) > 0;

    if ($tieneTurnos) {
      return $this->response->setStatusCode(409)->setJSON([
        'status' => 'tiene_turnos',
        'message' => 'El empleado tiene turnos asignados.',
      ]);
    }

    $this->cancelarSolicitudesPendientesDelUsuario($idUsuario);

    $eliminado = $this->usuarioModel->delete($idUsuario);

    if (!$eliminado) {
      return $this->response->setStatusCode(500)->setJSON([
        'status' => 'error',
        'message' => 'No se pudo eliminar el usuario.',
      ]);
    }

    return $this->response->setJSON([
      'status' => 'success',
      'message' => 'Usuario eliminado correctamente.',
    ]);
  }
  /**
   * Devuelve los horarios donde el empleado tiene turnos asignados
   * @param int $idUsuario
   * @return ResponseInterface
   */
  public function horariosDeEmpleado(int $idUsuario): ResponseInterface
  {
    $errorPermisos = $this->exigirAdministrador();

    if ($errorPermisos !== null) {
      return $errorPermisos;
    }

    $usuario = $this->usuarioModel->getUsuarioPorId($idUsuario);

    if (!$usuario) {
      return $this->responderNoEncontrado();
    }

    $errorEmpresa = $this->validarUsuarioPerteneceAEmpresaActual($usuario);

    if ($errorEmpresa !== null) {
      return $errorEmpresa;
    }

    $horarios = $this->turnoModel->getHorariosConTurnosPorUsuario($idUsuario);

    return $this->response->setJSON([
      'status' => 'success',
      'data'   => $horarios,
    ]);
  }

  /**
   * Libera turnos del empleado en los horarios seleccionados y lo desactiva
   * @param int $idUsuario
   * @return ResponseInterface
   */
  public function desactivarConLiberar(int $idUsuario): ResponseInterface
  {
    $errorPermisos = $this->exigirAdministrador();

    if ($errorPermisos !== null) {
      return $errorPermisos;
    }

    $usuario = $this->usuarioModel->getUsuarioPorId($idUsuario);

    if (!$usuario) {
      return $this->responderNoEncontrado();
    }

    $errorEmpresa = $this->validarUsuarioPerteneceAEmpresaActual($usuario);

    if ($errorEmpresa !== null) {
      return $errorEmpresa;
    }

    $idsHorarios = $this->request->getPost('horarios');

    if (!empty($idsHorarios) && is_array($idsHorarios)) {
      $this->turnoModel->liberarTurnosDeUsuarioPorHorarios($idUsuario, $idsHorarios);
    }

    $this->usuarioModel->update($idUsuario, ['usu_activo' => 0]);

    return $this->response->setJSON([
      'status'  => 'success',
      'message' => 'Empleado desactivado correctamente.',
    ]);
  }

  /**
   * Libera los turnos del usuario y luego lo elimina
   * @param int $idUsuario
   * @return ResponseInterface
   */
  public function liberarYEliminar(int $idUsuario): ResponseInterface
  {
    $errorPermisos = $this->exigirAdministrador();

    if ($errorPermisos !== null) {
      return $errorPermisos;
    }

    $usuario = $this->usuarioModel->getUsuarioPorId($idUsuario);

    if (!$usuario) {
      return $this->responderNoEncontrado();
    }

    $errorEmpresa = $this->validarUsuarioPerteneceAEmpresaActual($usuario);

    if ($errorEmpresa !== null) {
      return $errorEmpresa;
    }

    $this->turnoModel->liberarTurnosDeUsuario($idUsuario);
    $this->cancelarSolicitudesPendientesDelUsuario($idUsuario);

    $eliminado = $this->usuarioModel->delete($idUsuario);

    if (!$eliminado) {
      return $this->response->setStatusCode(500)->setJSON([
        'status' => 'error',
        'message' => 'No se pudo eliminar el usuario.',
      ]);
    }

    return $this->response->setJSON([
      'status' => 'success',
      'message' => 'Turnos liberados y usuario eliminado correctamente.',
    ]);
  }

  /**
   * Cancela todas las solicitudes pendientes relacionadas con el usuario
   * @param int $idUsuario
   * @return void
   */
  private function cancelarSolicitudesPendientesDelUsuario(int $idUsuario): void
  {
    $this->db->table('solicitudes_cambio_turno')
      ->groupStart()
      ->where('sol_id_usuario_solicitante', $idUsuario)
      ->orWhere('sol_id_usuario_destinatario', $idUsuario)
      ->groupEnd()
      ->where('sol_estado', 'pendiente')
      ->update([
        'sol_estado' => 'cancelada',
        'sol_fecha_resolucion' => date('Y-m-d H:i:s'),
        'sol_comentario_resolucion' => 'Cancelada automáticamente por eliminación del usuario.',
      ]);
  }

  /**
   * Devuelve las reglas de validación del usuario
   * @param bool $passwordObligatoria
   * @return array
   */
  private function obtenerReglasUsuario(bool $passwordObligatoria = true): array
  {
    $reglasPassword = $passwordObligatoria
      ? 'required|min_length[8]'
      : 'permit_empty|min_length[8]';

    return [
      'usu_nombre' => 'required|min_length[2]|max_length[100]',
      'usu_apellidos' => 'required|min_length[2]|max_length[150]',
      'usu_email' => 'required|valid_email|max_length[150]',
      'usu_password' => $reglasPassword,
      'usu_rol' => 'required|in_list[administrador,empleado]',
      'usu_activo' => 'permit_empty|in_list[0,1]',
    ];
  }

  /**
   * Comprueba que el usuario haya iniciado sesión
   * @return ResponseInterface|null
   */
  private function exigirLogin(): ?ResponseInterface
  {
    if (!session()->get('isLoggedIn')) {
      return $this->response->setStatusCode(401)->setJSON([
        'status' => 'error',
        'message' => 'Debes iniciar sesión.',
      ]);
    }

    return null;
  }

  /**
   * Comprueba que el usuario logueado sea administrador
   * @return ResponseInterface|null
   */
  private function exigirAdministrador(): ?ResponseInterface
  {
    $errorLogin = $this->exigirLogin();

    if ($errorLogin !== null) {
      return $errorLogin;
    }

    if (session()->get('usu_rol') !== 'administrador') {
      return $this->response->setStatusCode(403)->setJSON([
        'status' => 'error',
        'message' => 'No tienes permisos para realizar esta acción.',
      ]);
    }

    return null;
  }

  /**
   * Comprueba que el usuario pertenezca a la empresa de la sesión
   * @param array $usuario
   * @return ResponseInterface|null
   */
  private function validarUsuarioPerteneceAEmpresaActual(array $usuario): ?ResponseInterface
  {
    if ((int) $usuario['usu_id_empresa'] !== (int) session()->get('usu_id_empresa')) {
      return $this->response->setStatusCode(403)->setJSON([
        'status' => 'error',
        'message' => 'No tienes acceso a este usuario.',
      ]);
    }

    return null;
  }

  /**
   * Devuelve respuesta de error de validación
   * @return ResponseInterface
   */
  private function responderErrorValidacion(): ResponseInterface
  {
    return $this->response->setStatusCode(400)->setJSON([
      'status' => 'error',
      'errors' => $this->validator->getErrors(),
    ]);
  }

  /**
   * Devuelve respuesta de no encontrado
   * @return ResponseInterface
   */
  private function responderNoEncontrado(): ResponseInterface
  {
    return $this->response->setStatusCode(404)->setJSON([
      'status' => 'error',
      'message' => 'Usuario no encontrado.',
    ]);
  }
}
