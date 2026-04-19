<?php

namespace App\Controllers;

use App\Models\TurnoModel;
use App\Models\HorarioModel;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class TurnosController extends BaseController
{
  protected TurnoModel $turnoModel;
  protected HorarioModel $horarioModel;
  protected UsuarioModel $usuarioModel;

  /**
   * Método de CodeIgniter que inicia el controlador
   * Crea una instancia de los modelos TurnoModel, HorarioModel y UsuarioModel
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
    $this->turnoModel = new TurnoModel();
    $this->horarioModel = new HorarioModel();
    $this->usuarioModel = new UsuarioModel();
  }

  /**
   * Devuelve un turno concreto, por su id, en formato JSON
   * Solo si pertenece a la empresa del usuario logueado
   * @param mixed $id
   * @return ResponseInterface
   */
  public function mostrar($id): ResponseInterface
  {
    $errorLogin = $this->exigirLogin();

    if ($errorLogin !== null) {
      return $errorLogin;
    }

    $turno = $this->turnoModel->getTurno((int) $id);

    if (!$turno) {
      return $this->responderNoEncontrado();
    }

    $errorEmpresa = $this->validarTurnoPerteneceAEmpresaActual($turno);

    if ($errorEmpresa !== null) {
      return $errorEmpresa;
    }

    return $this->response->setJSON($turno);
  }

  /**
   * Devuelve la lista de turnos de un horario en formato JSON
   * Solo si el horario pertenece a la empresa del usuario logueado
   * @param mixed $horarioId
   * @return ResponseInterface
   */
  public function listadoPorHorario($horarioId): ResponseInterface
  {
    $errorLogin = $this->exigirLogin();

    if ($errorLogin !== null) {
      return $errorLogin;
    }

    $errorHorario = $this->validarHorarioExistenteYDeEmpresaActual((int) $horarioId);

    if ($errorHorario !== null) {
      return $errorHorario;
    }

    $turnos = $this->turnoModel->getTurnosPorHorario((int) $horarioId);

    return $this->response->setJSON($turnos);
  }

  /**
 * Devuelve los turnos del usuario logueado en sesión 
 * @return ResponseInterface
 */
public function misTurnos(): ResponseInterface
{
    $errorLogin = $this->exigirLogin();

    if ($errorLogin !== null) {
        return $errorLogin;
    }

    $idUsuario = (int) session()->get('usu_id_usuario');
    $turnos = $this->turnoModel->getTurnosPorUsuario($idUsuario);

    return $this->response->setJSON([
        'status' => 'success',
        'data' => $turnos,
    ]);
}

  /**
   * Devuelve los turnos de un horario en formato compatible con FullCalendar
   * Recibe horario_id por query string
   * Solo si el horario pertenece a la empresa del usuario logueado
   * @return ResponseInterface
   */
  public function eventos(): ResponseInterface
  {
    $errorLogin = $this->exigirLogin();

    if ($errorLogin !== null) {
      return $errorLogin;
    }

    $horarioId = (int) $this->request->getGet('horario_id');

    if ($horarioId <= 0) {
      return $this->response->setStatusCode(400)->setJSON([
        'status' => 'error',
        'message' => 'El parámetro horario_id es obligatorio.',
      ]);
    }

    $errorHorario = $this->validarHorarioExistenteYDeEmpresaActual($horarioId);

    if ($errorHorario !== null) {
      return $errorHorario;
    }

    $eventos = $this->turnoModel->getTurnosParaCalendario($horarioId);

    return $this->response->setJSON($eventos);
  }

  /**
   * Crea un turno nuevo en la empresa del administrador logueado
   * @return ResponseInterface
   */
  public function crear(): ResponseInterface
  {
    $errorPermisos = $this->exigirAdministrador();

    if ($errorPermisos !== null) {
      return $errorPermisos;
    }

    if (!$this->validate($this->obtenerReglasTurno())) {
      return $this->responderErrorValidacion();
    }

    $horarioId = (int) $this->request->getPost('tur_id_horario');
    $inicio = (string) $this->request->getPost('tur_inicio');
    $fin = (string) $this->request->getPost('tur_fin');

    $usuarioIdPost = $this->request->getPost('tur_id_usuario');
    $usuarioId = $usuarioIdPost !== null && $usuarioIdPost !== ''
      ? (int) $usuarioIdPost
      : null;

    $errorHorario = $this->validarHorarioExistenteYDeEmpresaActual($horarioId);

    if ($errorHorario !== null) {
      return $errorHorario;
    }

    $errorUsuario = $this->validarUsuarioExistenteYDeEmpresaActual($usuarioId);

    if ($errorUsuario !== null) {
      return $errorUsuario;
    }

    $errorFechas = $this->validarRangoFechasHoras($inicio, $fin);

    if ($errorFechas !== null) {
      return $errorFechas;
    }

    $estado = $this->request->getPost('tur_estado');

    if (!$estado) {
      $estado = $usuarioId === null ? 'disponible' : 'asignado';
    }

    $errorCoherencia = $this->validarCoherenciaUsuarioYEstado($usuarioId, $estado);

    if ($errorCoherencia !== null) {
      return $errorCoherencia;
    }

    $errorSolape = $this->validarSolapeTurnoUsuario($usuarioId, $inicio, $fin);

    if ($errorSolape !== null) {
      return $errorSolape;
    }

    $datos = [
      'tur_id_horario' => $horarioId,
      'tur_id_usuario' => $usuarioId,
      'tur_inicio' => $inicio,
      'tur_fin' => $fin,
      'tur_estado' => $estado,
      'tur_observaciones' => $this->request->getPost('tur_observaciones'),
    ];

    $id = $this->turnoModel->insert($datos);

    if (!$id) {
      return $this->response->setStatusCode(500)->setJSON([
        'status' => 'error',
        'message' => 'No se pudo crear el turno.',
      ]);
    }

    $turnoCreado = $this->turnoModel->getTurno((int) $id);

    return $this->response->setStatusCode(201)->setJSON([
      'status' => 'success',
      'message' => 'Turno creado correctamente.',
      'data' => $turnoCreado,
    ]);
  }

  /**
   * Actualiza un turno existente de la empresa del administrador logueado
   * @param mixed $id
   * @return ResponseInterface
   */
  public function actualizar($id): ResponseInterface
  {
    $errorPermisos = $this->exigirAdministrador();

    if ($errorPermisos !== null) {
      return $errorPermisos;
    }

    $turno = $this->turnoModel->getTurno((int) $id);

    if (!$turno) {
      return $this->responderNoEncontrado();
    }

    $errorEmpresaTurno = $this->validarTurnoPerteneceAEmpresaActual($turno);

    if ($errorEmpresaTurno !== null) {
      return $errorEmpresaTurno;
    }

    if (!$this->validate($this->obtenerReglasTurno())) {
      return $this->responderErrorValidacion();
    }

    $horarioId = (int) $this->request->getPost('tur_id_horario');
    $inicio = (string) $this->request->getPost('tur_inicio');
    $fin = (string) $this->request->getPost('tur_fin');

    $usuarioIdPost = $this->request->getPost('tur_id_usuario');
    $usuarioId = $usuarioIdPost !== null && $usuarioIdPost !== ''
      ? (int) $usuarioIdPost
      : null;

    $errorHorario = $this->validarHorarioExistenteYDeEmpresaActual($horarioId);

    if ($errorHorario !== null) {
      return $errorHorario;
    }

    $errorUsuario = $this->validarUsuarioExistenteYDeEmpresaActual($usuarioId);

    if ($errorUsuario !== null) {
      return $errorUsuario;
    }

    $errorFechas = $this->validarRangoFechasHoras($inicio, $fin);

    if ($errorFechas !== null) {
      return $errorFechas;
    }

    $estado = $this->request->getPost('tur_estado');

    if (!$estado) {
      $estado = $usuarioId === null ? 'disponible' : 'asignado';
    }

    $errorCoherencia = $this->validarCoherenciaUsuarioYEstado($usuarioId, $estado);

    if ($errorCoherencia !== null) {
      return $errorCoherencia;
    }

    $errorSolape = $this->validarSolapeTurnoUsuario($usuarioId, $inicio, $fin, (int) $id);

    if ($errorSolape !== null) {
      return $errorSolape;
    }

    $datos = [
      'tur_id_horario' => $horarioId,
      'tur_id_usuario' => $usuarioId,
      'tur_inicio' => $inicio,
      'tur_fin' => $fin,
      'tur_estado' => $estado,
      'tur_observaciones' => $this->request->getPost('tur_observaciones'),
    ];

    $actualizado = $this->turnoModel->update((int) $id, $datos);

    if (!$actualizado) {
      return $this->response->setStatusCode(500)->setJSON([
        'status' => 'error',
        'message' => 'No se pudo actualizar el turno.',
      ]);
    }

    $turnoActualizado = $this->turnoModel->getTurno((int) $id);

    return $this->response->setJSON([
      'status' => 'success',
      'message' => 'Turno actualizado correctamente.',
      'data' => $turnoActualizado,
    ]);
  }

  /**
   * Elimina un turno de la empresa del administrador logueado
   * @param mixed $id
   * @return ResponseInterface
   */
  public function eliminar($id): ResponseInterface
  {
    $errorPermisos = $this->exigirAdministrador();

    if ($errorPermisos !== null) {
      return $errorPermisos;
    }

    $turno = $this->turnoModel->getTurno((int) $id);

    if (!$turno) {
      return $this->responderNoEncontrado();
    }

    $errorEmpresaTurno = $this->validarTurnoPerteneceAEmpresaActual($turno);

    if ($errorEmpresaTurno !== null) {
      return $errorEmpresaTurno;
    }

    $eliminado = $this->turnoModel->delete((int) $id);

    if (!$eliminado) {
      return $this->response->setStatusCode(500)->setJSON([
        'status' => 'error',
        'message' => 'No se pudo eliminar el turno.',
      ]);
    }

    return $this->response->setJSON([
      'status' => 'success',
      'message' => 'Turno eliminado correctamente.',
    ]);
  }

  /**
   * Aplica las reglas de validación para turnos
   * tur_estado es obligatorio o no según el parámetro que reciba
   * @return array
   */
  private function obtenerReglasTurno(): array
  {
    return [
      'tur_id_horario' => 'required|integer',
      'tur_id_usuario' => 'permit_empty|integer',
      'tur_inicio' => 'required|valid_date',
      'tur_fin' => 'required|valid_date',
      'tur_estado' => 'permit_empty|in_list[asignado,disponible,pendiente_cambio,cambiado,cancelado]',
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
   * Comprueba que la fecha y hora de fin no sea anterior ni igual a la de inicio
   * @param string $inicio
   * @param string $fin
   * @return ResponseInterface|null
   */
  private function validarRangoFechasHoras(string $inicio, string $fin): ?ResponseInterface
  {
    if ($fin <= $inicio) {
      return $this->response->setStatusCode(400)->setJSON([
        'status' => 'error',
        'message' => 'La fecha y hora de fin debe ser mayor que la de inicio.',
      ]);
    }

    return null;
  }

  /**
   * Comprueba que el horario exista y pertenezca a la empresa actual
   * @param int $horarioId
   * @return ResponseInterface|null
   */
  private function validarHorarioExistenteYDeEmpresaActual(int $horarioId): ?ResponseInterface
  {
    $horario = $this->horarioModel->getHorario($horarioId);

    if (!$horario) {
      return $this->response->setStatusCode(404)->setJSON([
        'status' => 'error',
        'message' => 'Horario no encontrado.',
      ]);
    }

    if ((int) $horario['hor_id_empresa'] !== (int) session()->get('usu_id_empresa')) {
      return $this->response->setStatusCode(403)->setJSON([
        'status' => 'error',
        'message' => 'No tienes acceso a este horario.',
      ]);
    }

    return null;
  }

  /**
   * Comprueba que el usuario exista y pertenezca a la empresa actual
   * @param int|null $usuarioId
   * @return ResponseInterface|null
   */
  private function validarUsuarioExistenteYDeEmpresaActual(?int $usuarioId): ?ResponseInterface
  {
    if ($usuarioId === null) {
      return null;
    }

    $usuario = $this->usuarioModel->getUsuarioPorId($usuarioId);

    if (!$usuario) {
      return $this->response->setStatusCode(404)->setJSON([
        'status' => 'error',
        'message' => 'Usuario no encontrado.',
      ]);
    }

    if ((int) $usuario['usu_id_empresa'] !== (int) session()->get('usu_id_empresa')) {
      return $this->response->setStatusCode(403)->setJSON([
        'status' => 'error',
        'message' => 'No tienes acceso a este usuario.',
      ]);
    }

    return null;
  }

  /**
   * Comprueba que el estado del turno sea coherente con el usuario asignado
   * @param int|null $usuarioId
   * @param string $estado
   * @return ResponseInterface|null
   */
  private function validarCoherenciaUsuarioYEstado(?int $usuarioId, string $estado): ?ResponseInterface
  {
    if ($usuarioId === null && $estado === 'asignado') {
      return $this->response->setStatusCode(400)->setJSON([
        'status' => 'error',
        'message' => 'Un turno sin usuario no puede estar asignado.',
      ]);
    }

    if ($usuarioId !== null && $estado === 'disponible') {
      return $this->response->setStatusCode(400)->setJSON([
        'status' => 'error',
        'message' => 'Un turno con usuario no puede estar disponible.',
      ]);
    }

    return null;
  }

  /**
   * Comprueba que el usuario no tenga otro turno solapado en la misma franja
   * @param int|null $usuarioId
   * @param string $inicio
   * @param string $fin
   * @param int|null $turnoIdExcluir
   * @return ResponseInterface|null
   */
  private function validarSolapeTurnoUsuario(
    ?int $usuarioId,
    string $inicio,
    string $fin,
    ?int $turnoIdExcluir = null
  ): ?ResponseInterface {
    if ($usuarioId === null) {
      return null;
    }

    $haySolape = $this->turnoModel->existeSolapeUsuario(
      $usuarioId,
      $inicio,
      $fin,
      $turnoIdExcluir
    );

    if ($haySolape) {
      return $this->response->setStatusCode(400)->setJSON([
        'status' => 'error',
        'message' => 'El usuario ya tiene otro turno asignado en una franja horaria solapada.',
      ]);
    }

    return null;
  }

  /**
   * Comprueba que el turno pertenezca a la empresa actual a través de su horario
   * @param array $turno
   * @return ResponseInterface|null
   */
  private function validarTurnoPerteneceAEmpresaActual(array $turno): ?ResponseInterface
  {
    $horario = $this->horarioModel->getHorario((int) $turno['tur_id_horario']);

    if (!$horario) {
      return $this->response->setStatusCode(404)->setJSON([
        'status' => 'error',
        'message' => 'El horario del turno no existe.',
      ]);
    }

    if ((int) $horario['hor_id_empresa'] !== (int) session()->get('usu_id_empresa')) {
      return $this->response->setStatusCode(403)->setJSON([
        'status' => 'error',
        'message' => 'No tienes acceso a este turno.',
      ]);
    }

    return null;
  }

  /**
   * Si no encuentra lo que busca, responde un JSON con 404
   * @return ResponseInterface
   */
  private function responderNoEncontrado(): ResponseInterface
  {
    return $this->response->setStatusCode(404)->setJSON([
      'status' => 'error',
      'message' => 'Turno no encontrado.',
    ]);
  }
}
