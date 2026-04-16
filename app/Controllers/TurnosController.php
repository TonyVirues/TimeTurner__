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
  protected $turnoModel;
  protected $horarioModel;
  protected $usuarioModel;

  /**
   * Método de CodeIgniter que inicia el controlador
   * Crea una instancia de los modelos TurnoModel, HorarioModel y UsuarioModel
   * @param RequestInterface $request
   * @param ResponseInterface $response
   * @param LoggerInterface $logger
   * @return void
   */
  public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
  {
    parent::initController($request, $response, $logger);
    $this->turnoModel = new TurnoModel();
    $this->horarioModel = new HorarioModel();
    $this->usuarioModel = new UsuarioModel();
  }

  /**
   * Devuelve un turno concreto, por su id, en formato JSON
   * @param mixed $id
   * @return ResponseInterface
   */
  public function mostrar($id)
  {
    $turno = $this->turnoModel->getTurno((int) $id);

    if (! $turno) {
      return $this->responderNoEncontrado();
    }

    return $this->response->setJSON($turno);
  }

  /**
   * Devuelve la lista de turnos de un horario en formato JSON
   * @param mixed $horarioId
   * @return ResponseInterface
   */
  public function listadoPorHorario($horarioId)
  {
    $errorHorario = $this->validarHorarioExistente((int) $horarioId);

    if ($errorHorario !== null) {
      return $errorHorario;
    }

    $turnos = $this->turnoModel->getTurnosPorHorario((int) $horarioId);

    return $this->response->setJSON($turnos);
  }

  /**
   * Devuelve los turnos de un horario en formato compatible con FullCalendar
   * Recibe horario_id por query string
   * @return ResponseInterface
   */
  public function eventos()
  {
    $horarioId = (int) $this->request->getGet('horario_id');

    if ($horarioId <= 0) {
      return $this->response->setStatusCode(400)->setJSON([
        'status' => 'error',
        'message' => 'El parámetro horario_id es obligatorio.',
      ]);
    }

    $errorHorario = $this->validarHorarioExistente($horarioId);

    if ($errorHorario !== null) {
      return $errorHorario;
    }

    $eventos = $this->turnoModel->getTurnosParaCalendario($horarioId);

    return $this->response->setJSON($eventos);
  }

  /**
   * Llama a los métodos para validar los datos que recibe del front
   * Crea un turno nuevo en la db
   * Lo devuelve en formato JSON
   * @return ResponseInterface
   */
  public function crear()
  {
    if (! $this->validate($this->obtenerReglasTurno())) {
      return $this->responderErrorValidacion();
    }

    $horarioId = (int) $this->request->getPost('tur_id_horario');
    $inicio = $this->request->getPost('tur_inicio');
    $fin = $this->request->getPost('tur_fin');

    $usuarioIdPost = $this->request->getPost('tur_id_usuario');
    $usuarioId = $usuarioIdPost !== null && $usuarioIdPost !== ''
      ? (int) $usuarioIdPost
      : null;

    $errorHorario = $this->validarHorarioExistente($horarioId);
    //La función validarHorarioExistente devuelve una respuesta HTTP si hay error y null si todo va bien
    if ($errorHorario !== null) {
      return $errorHorario;
    }

    $errorUsuario = $this->validarUsuarioExistente($usuarioId);
    if ($errorUsuario !== null) {
      return $errorUsuario;
    }

    $errorFechas = $this->validarRangoFechasHoras($inicio, $fin);
    if ($errorFechas !== null) {
      return $errorFechas;
    }

    $estado = $this->request->getPost('tur_estado');

    if (! $estado) {
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

    if (! $id) {
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
   * Busca un turno por su id
   * Llama a los métodos para validar los datos
   * Actualiza el turno en la db y lo devuelve en formato JSON
   * @param mixed $id
   * @return ResponseInterface
   */
  public function actualizar($id)
  {
    $turno = $this->turnoModel->getTurno((int) $id);

    if (! $turno) {
      return $this->responderNoEncontrado();
    }

    if (! $this->validate($this->obtenerReglasTurno())) {
      return $this->responderErrorValidacion();
    }

    $horarioId = (int) $this->request->getPost('tur_id_horario');
    $inicio = $this->request->getPost('tur_inicio');
    $fin = $this->request->getPost('tur_fin');

    $usuarioIdPost = $this->request->getPost('tur_id_usuario');
    $usuarioId = $usuarioIdPost !== null && $usuarioIdPost !== ''
      ? (int) $usuarioIdPost
      : null;

    $errorHorario = $this->validarHorarioExistente($horarioId);
    if ($errorHorario !== null) {
      return $errorHorario;
    }

    $errorUsuario = $this->validarUsuarioExistente($usuarioId);
    if ($errorUsuario !== null) {
      return $errorUsuario;
    }

    $errorFechas = $this->validarRangoFechasHoras($inicio, $fin);
    if ($errorFechas !== null) {
      return $errorFechas;
    }

    $estado = $this->request->getPost('tur_estado');

    if (! $estado) {
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

    if (! $actualizado) {
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
   * Busca un turno por id, lo elimina de la db
   * Devuelve respuesta JSON de si lo borra o de error
   * @param mixed $id
   * @return ResponseInterface
   */
  public function eliminar($id)
  {
    $turno = $this->turnoModel->getTurno((int) $id);

    if (! $turno) {
      return $this->responderNoEncontrado();
    }

    $eliminado = $this->turnoModel->delete((int) $id);

    if (! $eliminado) {
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
   * @return array{tur_estado: string, tur_fin: string, tur_id_horario: string, tur_id_usuario: string, tur_inicio: string}
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
   * Devuelve una respuesta JSON con los errores de validación si los datos del usuario no cumplen las reglas
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
   * Devuelve JSON con el error
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
   * Comprueba que el horario al que pertenece el turno exista
   * @param int $horarioId
   * @return ResponseInterface|null
   */
  private function validarHorarioExistente(int $horarioId): ?ResponseInterface
  {
    $horario = $this->horarioModel->getHorario($horarioId);

    if (! $horario) {
      return $this->response->setStatusCode(404)->setJSON([
        'status' => 'error',
        'message' => 'Horario no encontrado.',
      ]);
    }

    return null;
  }

  /**
   * Comprueba que el usuario exista si se ha enviado uno
   * @param int|null $usuarioId
   * @return ResponseInterface|null
   */
  private function validarUsuarioExistente(?int $usuarioId): ?ResponseInterface
  {
    if ($usuarioId === null) {
      return null;
    }

    $usuario = $this->usuarioModel->find($usuarioId);

    if (! $usuario) {
      return $this->response->setStatusCode(404)->setJSON([
        'status' => 'error',
        'message' => 'Usuario no encontrado.',
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
  private function validarSolapeTurnoUsuario(?int $usuarioId, string $inicio, string $fin, ?int $turnoIdExcluir = null): ?ResponseInterface
  {
    if ($usuarioId === null) {
      return null;
    }

    $haySolape = $this->turnoModel->existeSolapeUsuario($usuarioId, $inicio, $fin, $turnoIdExcluir);

    if ($haySolape) {
      return $this->response->setStatusCode(400)->setJSON([
        'status' => 'error',
        'message' => 'El usuario ya tiene otro turno asignado en una franja horaria solapada.',
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
