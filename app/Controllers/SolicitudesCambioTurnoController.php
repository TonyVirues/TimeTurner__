<?php

namespace App\Controllers;

use App\Models\SolicitudCambioTurnoModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Exception;

class SolicitudesCambioTurnoController extends BaseController
{
  protected $solicitudCambioTurnoModel;

  /**
   * Inicializa el controlador y carga el modelo de solicitudes de cambio
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

    $this->solicitudCambioTurnoModel = new SolicitudCambioTurnoModel();
  }

  /**
   * Crea una nueva solicitud de cambio de turno
   * @return ResponseInterface
   */
  public function crear(): ResponseInterface
  {
    try {
      $idUsuarioSesion = $this->obtenerIdUsuarioSesion();
      $idEmpresaSesion = $this->obtenerIdEmpresaSesion();

      $datos = [
        'sol_id_usuario_solicitante' => (int) $this->request->getPost('sol_id_usuario_solicitante'),
        'sol_id_usuario_destinatario' => (int) $this->request->getPost('sol_id_usuario_destinatario'),
        'sol_id_turno_original' => (int) $this->request->getPost('sol_id_turno_original'),
        'sol_id_turno_propuesto' => (int) $this->request->getPost('sol_id_turno_propuesto'),
        'sol_motivo' => $this->request->getPost('sol_motivo'),
      ];

      $solicitud = $this->solicitudCambioTurnoModel->crearSolicitud(
        $datos,
        $idUsuarioSesion,
        $idEmpresaSesion
      );

      return $this->response->setStatusCode(201)->setJSON([
        'ok' => true,
        'mensaje' => 'Solicitud de cambio creada correctamente.',
        'data' => $solicitud,
      ]);
    } catch (Exception $e) {
      return $this->response->setStatusCode($this->obtenerCodigoHttpDesdeExcepcion($e))->setJSON([
        'ok' => false,
        'mensaje' => $e->getMessage(),
      ]);
    }
  }

  /**
   * Muestra una solicitud concreta por id
   * @param int $id
   * @return ResponseInterface
   */
  public function mostrar(int $id): ResponseInterface
  {
    try {
      $idUsuarioSesion = $this->obtenerIdUsuarioSesion();
      $idEmpresaSesion = $this->obtenerIdEmpresaSesion();
      $rolSesion = $this->obtenerRolSesion();

      $solicitud = $this->solicitudCambioTurnoModel->obtenerSolicitudVisibleParaUsuario(
        $id,
        $idUsuarioSesion,
        $idEmpresaSesion,
        $rolSesion
      );

      return $this->response->setJSON([
        'ok' => true,
        'data' => $solicitud,
      ]);
    } catch (Exception $e) {
      return $this->response->setStatusCode($this->obtenerCodigoHttpDesdeExcepcion($e))->setJSON([
        'ok' => false,
        'mensaje' => $e->getMessage(),
      ]);
    }
  }

  /**
   * Devuelve el listado de solicitudes
   * Se puede filtrar por estado y usuarios
   * @return ResponseInterface
   */
  public function listado(): ResponseInterface
  {
    try {
      $idUsuarioSesion = $this->obtenerIdUsuarioSesion();
      $idEmpresaSesion = $this->obtenerIdEmpresaSesion();
      $rolSesion = $this->obtenerRolSesion();

      $filtros = [
        'sol_id_usuario_solicitante' => $this->request->getGet('sol_id_usuario_solicitante'),
        'sol_id_usuario_destinatario' => $this->request->getGet('sol_id_usuario_destinatario'),
        'sol_estado' => $this->request->getGet('sol_estado'),
      ];

      $solicitudes = $this->solicitudCambioTurnoModel->obtenerSolicitudesVisiblesParaUsuario(
        $idUsuarioSesion,
        $idEmpresaSesion,
        $rolSesion,
        $filtros
      );

      return $this->response->setJSON([
        'ok' => true,
        'data' => $solicitudes,
      ]);
    } catch (Exception $e) {
      return $this->response->setStatusCode($this->obtenerCodigoHttpDesdeExcepcion($e))->setJSON([
        'ok' => false,
        'mensaje' => $e->getMessage(),
      ]);
    }
  }

  /**
   * Acepta una solicitud pendiente
   * @param int $id
   * @return ResponseInterface
   */
  public function aceptar(int $id): ResponseInterface
  {
    try {
      $idUsuarioSesion = $this->obtenerIdUsuarioSesion();
      $idEmpresaSesion = $this->obtenerIdEmpresaSesion();

      $solicitud = $this->solicitudCambioTurnoModel->aceptarSolicitud(
        $id,
        $idUsuarioSesion,
        $idEmpresaSesion
      );

      return $this->response->setJSON([
        'ok' => true,
        'mensaje' => 'Solicitud aceptada correctamente.',
        'data' => $solicitud,
      ]);
    } catch (Exception $e) {
      return $this->response->setStatusCode($this->obtenerCodigoHttpDesdeExcepcion($e))->setJSON([
        'ok' => false,
        'mensaje' => $e->getMessage(),
      ]);
    }
  }

  /**
   * Rechaza una solicitud
   * @param int $id
   * @return ResponseInterface
   */
  public function rechazar(int $id): ResponseInterface
  {
    try {
      $idUsuarioSesion = $this->obtenerIdUsuarioSesion();
      $idEmpresaSesion = $this->obtenerIdEmpresaSesion();
      $comentario = $this->request->getPost('sol_comentario_resolucion');

      $solicitud = $this->solicitudCambioTurnoModel->rechazarSolicitud(
        $id,
        $idUsuarioSesion,
        $idEmpresaSesion,
        $comentario
      );

      return $this->response->setJSON([
        'ok' => true,
        'mensaje' => 'Solicitud rechazada correctamente.',
        'data' => $solicitud,
      ]);
    } catch (Exception $e) {
      return $this->response->setStatusCode($this->obtenerCodigoHttpDesdeExcepcion($e))->setJSON([
        'ok' => false,
        'mensaje' => $e->getMessage(),
      ]);
    }
  }

  /**
   * Cancela una solicitud
   * @param int $id
   * @return ResponseInterface
   */
  public function cancelar(int $id): ResponseInterface
  {
    try {
      $idUsuarioSesion = $this->obtenerIdUsuarioSesion();
      $idEmpresaSesion = $this->obtenerIdEmpresaSesion();
      $comentario = $this->request->getPost('sol_comentario_resolucion');

      $solicitud = $this->solicitudCambioTurnoModel->cancelarSolicitud(
        $id,
        $idUsuarioSesion,
        $idEmpresaSesion,
        $comentario
      );

      return $this->response->setJSON([
        'ok' => true,
        'mensaje' => 'Solicitud cancelada correctamente.',
        'data' => $solicitud,
      ]);
    } catch (Exception $e) {
      return $this->response->setStatusCode($this->obtenerCodigoHttpDesdeExcepcion($e))->setJSON([
        'ok' => false,
        'mensaje' => $e->getMessage(),
      ]);
    }
  }

  /**
   * Devuelve el id del usuario autenticado
   * @return int
   * @throws Exception
   */
  private function obtenerIdUsuarioSesion(): int
  {
    if (!session()->get('isLoggedIn')) {
      throw new Exception('Debes iniciar sesión.', 401);
    }

    $idUsuario = session()->get('usu_id_usuario');

    if (!$idUsuario) {
      throw new Exception('No se ha encontrado el usuario de la sesión.', 401);
    }

    return (int) $idUsuario;
  }

  /**
   * Devuelve el id de la empresa del usuario autenticado
   * @return int
   * @throws Exception
   */
  private function obtenerIdEmpresaSesion(): int
  {
    if (!session()->get('isLoggedIn')) {
      throw new Exception('Debes iniciar sesión.', 401);
    }

    $idEmpresa = session()->get('usu_id_empresa');

    if (!$idEmpresa) {
      throw new Exception('No se ha encontrado la empresa de la sesión.', 401);
    }

    return (int) $idEmpresa;
  }

  /**
   * Devuelve el rol del usuario autenticado
   * @return string
   * @throws Exception
   */
  private function obtenerRolSesion(): string
  {
    if (!session()->get('isLoggedIn')) {
      throw new Exception('Debes iniciar sesión.', 401);
    }

    $rol = session()->get('usu_rol');

    if (!$rol) {
      throw new Exception('No se ha encontrado el rol de la sesión.', 401);
    }

    return (string) $rol;
  }

  /**
   * Traduce el código de una excepción a un código HTTP válido
   * @param Exception $e
   * @return int
   */
  private function obtenerCodigoHttpDesdeExcepcion(Exception $e): int
  {
    $codigo = $e->getCode();

    if (in_array($codigo, [400, 401, 403, 404, 409, 422], true)) {
      return $codigo;
    }

    return 500;
  }
}
