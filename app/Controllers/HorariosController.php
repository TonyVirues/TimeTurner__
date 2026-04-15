<?php

namespace App\Controllers;

use App\Models\HorarioModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class HorariosController extends BaseController
{
  protected $horarioModel;

  /**
   * Método de CodeIgniter que inicia el controlador
   * Crea una instancia del modelo HorarioModel
   * @param RequestInterface $request
   * @param ResponseInterface $response
   * @param LoggerInterface $logger
   * @return void
   */
  public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
  {
    parent::initController($request, $response, $logger);
    $this->horarioModel = new HorarioModel();
  }

  /**
   * Devuelve un horario concreto, por su id, en formato JSON
   * @param mixed $id
   * @return ResponseInterface
   */
  public function mostrar($id)
  {
    $horario = $this->horarioModel->getHorario((int) $id);

    if (! $horario) {
      return $this->responderNoEncontrado();
    }

    return $this->response->setJSON($horario);
  }

  /**
   * Llama a los métodos para validar los datos que recibe del front
   * Crea un horario nuevo en la db
   * Lo devuelve en formato JSON
   * @return ResponseInterface
   */
  public function crear()
  {
    if (! $this->validate($this->obtenerReglasHorario())) {
      return $this->responderErrorValidacion();
    }

    $fechaInicio = $this->request->getPost('hor_fecha_inicio');
    $fechaFin = $this->request->getPost('hor_fecha_fin');

    $errorFechas = $this->validarRangoFechas($fechaInicio, $fechaFin);
    if ($errorFechas !== null) {
      return $errorFechas;
    }

    $datos = [
      'hor_nombre' => $this->request->getPost('hor_nombre'),
      'hor_fecha_inicio' => $fechaInicio,
      'hor_fecha_fin' => $fechaFin,
      'hor_descripcion' => $this->request->getPost('hor_descripcion'),
      'hor_estado' => $this->request->getPost('hor_estado') ?: 'borrador',
      'hor_id_empresa' => 1, //@mar harcodeo, cambiar 1
    ];

    $id = $this->horarioModel->insert($datos);

    if (! $id) {
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
   * Busca un horario por su id
   * Llama a los métodos para validar los datos
   * Actualiza el horario en la db y lo devuelve en formato JSON
   * @param mixed $id
   * @return ResponseInterface
   */
  public function actualizar($id)
  {
    $horario = $this->horarioModel->getHorario((int) $id);

    if (! $horario) {
      return $this->responderNoEncontrado();
    }

    if (! $this->validate($this->obtenerReglasHorario(true))) {
      return $this->responderErrorValidacion();
    }

    $fechaInicio = $this->request->getPost('hor_fecha_inicio');
    $fechaFin = $this->request->getPost('hor_fecha_fin');

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

    if (! $actualizado) {
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
   * Busca un horario por id, lo elimina de la db
   * Devuelve respuesta JSON de si lo borra o de error
   * @param mixed $id
   * @return ResponseInterface
   */
  public function eliminar($id)
  {
    $horario = $this->horarioModel->getHorario((int) $id);

    if (! $horario) {
      return $this->responderNoEncontrado();
    }

    $eliminado = $this->horarioModel->delete((int) $id);

    if (! $eliminado) {
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
   * Obtiene la lista de horarios desde el modelo y la devuelve en JSON para que la use el selector
   * @return ResponseInterface
   */
  public function listado()
  {
    $horarios = $this->horarioModel->getHorariosListado();

    return $this->response->setJSON($horarios);
  }

  /**
   * Aplica las reglas de validación para horarios
   * hor_estado es obligatorio o no según el parámetro que reciba
   * @param bool $estadoObligatorio
   * @return array{hor_estado: string, hor_fecha_fin: string, hor_fecha_inicio: string, hor_nombre: string}
   */
  private function obtenerReglasHorario(bool $estadoObligatorio = false): array
  {
    return [
      'hor_nombre' => 'required|min_length[3]|max_length[150]',
      'hor_fecha_inicio' => 'required|valid_date',
      'hor_fecha_fin' => 'required|valid_date',
      'hor_estado' => $estadoObligatorio ? 'required|in_list[borrador,publicado,cerrado]' : 'permit_empty|in_list[borrador,publicado,cerrado]',
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
   * Comprueba que la fecha de fin no sea anterior a la de inicio
   * Devuelve JSON con el error
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
