<?php

namespace App\Controllers;

use App\Models\HorarioModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class HorariosController extends BaseController
{
  protected HorarioModel $horarioModel;

  /**
   * Método de CodeIgniter que inicia el controlador
   * Crea una instancia del modelo HorarioModel
   * @param RequestInterface $request
   * @param ResponseInterface $response
   * @param LoggerInterface $logger
   * @return void
   */
  public function initController(
    RequestInterface $request,
    ResponseInterface $response,
    LoggerInterface $logger
  ) {
    parent::initController($request, $response, $logger);
    $this->horarioModel = new HorarioModel();
  }

  /**
   * Devuelve un horario concreto de la empresa del usuario logueado
   * @param mixed $id
   * @return ResponseInterface
   */
  public function mostrar($id): ResponseInterface
  {
    $errorLogin = $this->exigirLogin();

    if ($errorLogin !== null) {
      return $errorLogin;
    }

    $horario = $this->horarioModel->getHorario((int) $id);

    if (!$horario) {
      return $this->responderNoEncontrado();
    }

    $errorEmpresa = $this->validarHorarioPerteneceAEmpresaActual($horario);

    if ($errorEmpresa !== null) {
      return $errorEmpresa;
    }

    return $this->response->setJSON([
      'status' => 'success',
      'data' => $horario,
    ]);
  }

  /**
   * Crea un horario nuevo en la empresa del administrador logueado
   * @return ResponseInterface
   */
  public function crear(): ResponseInterface
  {
    $errorPermisos = $this->exigirAdministrador();

    if ($errorPermisos !== null) {
      return $errorPermisos;
    }

    if (!$this->validate($this->obtenerReglasHorario())) {
      return $this->responderErrorValidacion();
    }

    $fechaInicio = (string) $this->request->getPost('hor_fecha_inicio');
    $fechaFin = (string) $this->request->getPost('hor_fecha_fin');

    $errorFechas = $this->validarRangoFechas($fechaInicio, $fechaFin);

    if ($errorFechas !== null) {
      return $errorFechas;
    }

    $datos = [
      'hor_id_empresa' => (int) session()->get('usu_id_empresa'),
      'hor_nombre' => $this->request->getPost('hor_nombre'),
      'hor_fecha_inicio' => $fechaInicio,
      'hor_fecha_fin' => $fechaFin,
      'hor_descripcion' => $this->request->getPost('hor_descripcion'),
      'hor_estado' => $this->request->getPost('hor_estado') ?: 'borrador',
    ];

    $id = $this->horarioModel->insert($datos);

    if (!$id) {
      return $this->response->setStatusCode(500)->setJSON([
        'status' => 'error',
        'message' => 'No se pudo crear el horario.',
      ]);
    }

    $horarioCreado = $this->horarioModel->getHorario((int) $id);

    return $this->response->setStatusCode(201)->setJSON([
      'status' => 'success',
      'message' => 'Horario creado correctamente.',
      'data' => $horarioCreado,
    ]);
  }

  /**
   * Actualiza un horario existente de la empresa del administrador logueado
   * @param mixed $id
   * @return ResponseInterface
   */
  public function actualizar($id): ResponseInterface
  {
    $errorPermisos = $this->exigirAdministrador();

    if ($errorPermisos !== null) {
      return $errorPermisos;
    }

    $horario = $this->horarioModel->getHorario((int) $id);

    if (!$horario) {
      return $this->responderNoEncontrado();
    }

    $errorEmpresa = $this->validarHorarioPerteneceAEmpresaActual($horario);

    if ($errorEmpresa !== null) {
      return $errorEmpresa;
    }

    if (!$this->validate($this->obtenerReglasHorario(true))) {
      return $this->responderErrorValidacion();
    }

    $fechaInicio = (string) $this->request->getPost('hor_fecha_inicio');
    $fechaFin = (string) $this->request->getPost('hor_fecha_fin');

    $errorFechas = $this->validarRangoFechas($fechaInicio, $fechaFin);

    if ($errorFechas !== null) {
      return $errorFechas;
    }

    $datos = [
      'hor_nombre' => $this->request->getPost('hor_nombre'),
      'hor_fecha_inicio' => $fechaInicio,
      'hor_fecha_fin' => $fechaFin,
      'hor_descripcion' => $this->request->getPost('hor_descripcion'),
      'hor_estado' => $this->request->getPost('hor_estado'),
    ];

    $actualizado = $this->horarioModel->update((int) $id, $datos);

    if (!$actualizado) {
      return $this->response->setStatusCode(500)->setJSON([
        'status' => 'error',
        'message' => 'No se pudo actualizar el horario.',
      ]);
    }

    $horarioActualizado = $this->horarioModel->getHorario((int) $id);

    return $this->response->setJSON([
      'status' => 'success',
      'message' => 'Horario actualizado correctamente.',
      'data' => $horarioActualizado,
    ]);
  }

  /**
   * Elimina un horario de la empresa del administrador logueado
   * @param mixed $id
   * @return ResponseInterface
   */
  public function eliminar($id): ResponseInterface
  {
    $errorPermisos = $this->exigirAdministrador();

    if ($errorPermisos !== null) {
      return $errorPermisos;
    }

    $horario = $this->horarioModel->getHorario((int) $id);

    if (!$horario) {
      return $this->responderNoEncontrado();
    }

    $errorEmpresa = $this->validarHorarioPerteneceAEmpresaActual($horario);

    if ($errorEmpresa !== null) {
      return $errorEmpresa;
    }

    $eliminado = $this->horarioModel->delete((int) $id);

    if (!$eliminado) {
      return $this->response->setStatusCode(500)->setJSON([
        'status' => 'error',
        'message' => 'No se pudo eliminar el horario.',
      ]);
    }

    return $this->response->setJSON([
      'status' => 'success',
      'message' => 'Horario eliminado correctamente.',
    ]);
  }

  /**
   * Devuelve la lista de horarios de la empresa del usuario logueado
   * @return ResponseInterface
   */
public function listado(): ResponseInterface
{
    $errorLogin = $this->exigirLogin();

    if ($errorLogin !== null) {
        return $errorLogin;
    }

    $idEmpresa = (int) session()->get('usu_id_empresa');
    $rol = session()->get('usu_rol');

    $query = $this->horarioModel
        ->where('hor_id_empresa', $idEmpresa)
        ->orderBy('hor_fecha_inicio', 'ASC');

    if ($rol !== 'administrador') {
        $query->where('hor_estado', 'publicado');
    }

    $horarios = $query->findAll();

    return $this->response->setJSON($horarios);
}

  /**
   * Aplica las reglas de validación para horarios
   * hor_estado es obligatorio o no según el parámetro que reciba
   * @param bool $estadoObligatorio
   * @return array
   */
  private function obtenerReglasHorario(bool $estadoObligatorio = false): array
  {
    return [
      'hor_nombre' => 'required|min_length[3]|max_length[150]',
      'hor_fecha_inicio' => 'required|valid_date',
      'hor_fecha_fin' => 'required|valid_date',
      'hor_estado' => $estadoObligatorio
        ? 'required|in_list[borrador,publicado,cerrado]'
        : 'permit_empty|in_list[borrador,publicado,cerrado]',
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
   * Comprueba que la fecha de fin no sea anterior a la de inicio
   * @param string $fechaInicio
   * @param string $fechaFin
   * @return ResponseInterface|null
   */
  private function validarRangoFechas(string $fechaInicio, string $fechaFin): ?ResponseInterface
  {
    if ($fechaFin < $fechaInicio) {
      return $this->response->setStatusCode(400)->setJSON([
        'status' => 'error',
        'message' => 'La fecha de fin no puede ser menor que la fecha de inicio.',
      ]);
    }

    return null;
  }

  /**
   * Comprueba que el horario pertenezca a la empresa de la sesión
   * @param array $horario
   * @return ResponseInterface|null
   */
  private function validarHorarioPerteneceAEmpresaActual(array $horario): ?ResponseInterface
  {
    if ((int) $horario['hor_id_empresa'] !== (int) session()->get('usu_id_empresa')) {
      return $this->response->setStatusCode(403)->setJSON([
        'status' => 'error',
        'message' => 'No tienes acceso a este horario.',
      ]);
    }

    return null;
  }

  /**
   * Devuelve una respuesta JSON con los errores de validación
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
   * Si no encuentra lo que busca, responde un JSON con 404
   * @return ResponseInterface
   */
  private function responderNoEncontrado(): ResponseInterface
  {
    return $this->response->setStatusCode(404)->setJSON([
      'status' => 'error',
      'message' => 'Horario no encontrado.',
    ]);
  }
}
