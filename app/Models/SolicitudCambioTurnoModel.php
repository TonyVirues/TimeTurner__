<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class SolicitudCambioTurnoModel extends Model
{
  protected $table = 'solicitudes_cambio_turno';
  protected $primaryKey = 'sol_id_solicitud';
  protected $returnType = 'array';
  protected $useAutoIncrement = true;

  /**
   * Campos editables desde aplicación
   * @var array
   */
  protected $allowedFields = [
    'sol_id_usuario_solicitante',
    'sol_id_usuario_destinatario',
    'sol_id_turno_original',
    'sol_id_turno_propuesto',
    'sol_motivo',
    'sol_estado',
    'sol_fecha_resolucion',
    'sol_comentario_resolucion',
    'sol_visto',
  ];

  /**
   * La tabla usa columnas timestamp gestionadas por la BD
   * @var bool
   */
  protected $useTimestamps = false;

  /**
   * Crea una nueva solicitud de cambio de turno
   *
   * @param array $datos
   * @param int $idUsuarioSesion
   * @param int $idEmpresaSesion
   * @return array
   * @throws Exception
   */
  public function crearSolicitud(array $datos, int $idUsuarioSesion, int $idEmpresaSesion): array
  {
    $this->db->transBegin();

    try {
      $datosNormalizados = $this->normalizarDatosCreacion($datos);

      if ($datosNormalizados['sol_id_usuario_solicitante'] !== $idUsuarioSesion) {
        throw new Exception('No tienes permiso para crear una solicitud en nombre de otro usuario.', 403);
      }

      $solicitante = $this->obtenerUsuario($datosNormalizados['sol_id_usuario_solicitante']);
      $destinatario = $this->obtenerUsuario($datosNormalizados['sol_id_usuario_destinatario']);

      if (!$solicitante) {
        throw new Exception('El usuario solicitante no existe.', 404);
      }

      if (!$destinatario) {
        throw new Exception('El usuario destinatario no existe.', 404);
      }

      if ((int) $solicitante['usu_id_empresa'] !== (int) $destinatario['usu_id_empresa']) {
        throw new Exception('Los usuarios no pertenecen a la misma empresa.', 422);
      }

      if ((int) $solicitante['usu_id_empresa'] !== $idEmpresaSesion) {
        throw new Exception('No tienes permiso para crear solicitudes fuera de tu empresa.', 403);
      }

      if ((int) $solicitante['usu_id_usuario'] === (int) $destinatario['usu_id_usuario']) {
        throw new Exception('No se puede crear una solicitud contra el mismo usuario.', 422);
      }

      $turnoOriginal = $this->obtenerTurnoBloqueado($datosNormalizados['sol_id_turno_original']);
      $turnoPropuesto = $this->obtenerTurnoBloqueado($datosNormalizados['sol_id_turno_propuesto']);

      if (!$turnoOriginal) {
        throw new Exception('El turno original no existe.', 404);
      }

      if (!$turnoPropuesto) {
        throw new Exception('El turno propuesto no existe.', 404);
      }

      $this->validarTurnosMismaEmpresa($turnoOriginal, $turnoPropuesto, (int) $solicitante['usu_id_empresa']);

      if ((int) $turnoOriginal['hor_id_empresa'] !== $idEmpresaSesion || (int) $turnoPropuesto['hor_id_empresa'] !== $idEmpresaSesion) {
        throw new Exception('No tienes permiso para crear solicitudes con turnos de otra empresa.', 403);
      }

      $this->validarPropiedadTurnos(
        $turnoOriginal,
        $turnoPropuesto,
        (int) $solicitante['usu_id_usuario'],
        (int) $destinatario['usu_id_usuario']
      );

      $this->validarEstadosTurnosParaSolicitud($turnoOriginal, $turnoPropuesto);

      if ($this->existeSolicitudPendienteRelacionada(
        (int) $datosNormalizados['sol_id_turno_original'],
        (int) $datosNormalizados['sol_id_turno_propuesto']
      )) {
        throw new Exception('Ya existe una solicitud pendiente relacionada con uno de esos turnos.', 409);
      }

      $this->validarSolapesIntercambio(
        $turnoOriginal,
        $turnoPropuesto,
        (int) $solicitante['usu_id_usuario'],
        (int) $destinatario['usu_id_usuario']
      );

      $insertar = [
        'sol_id_usuario_solicitante' => $datosNormalizados['sol_id_usuario_solicitante'],
        'sol_id_usuario_destinatario' => $datosNormalizados['sol_id_usuario_destinatario'],
        'sol_id_turno_original' => $datosNormalizados['sol_id_turno_original'],
        'sol_id_turno_propuesto' => $datosNormalizados['sol_id_turno_propuesto'],
        'sol_motivo' => $datosNormalizados['sol_motivo'],
        'sol_estado' => 'pendiente',
      ];

      $this->insert($insertar);

      if (!$this->getInsertID()) {
        throw new Exception('No se pudo crear la solicitud.', 500);
      }

      $this->actualizarEstadoTurno((int) $turnoOriginal['tur_id_turno'], 'pendiente_cambio');
      $this->actualizarEstadoTurno((int) $turnoPropuesto['tur_id_turno'], 'pendiente_cambio');

      $this->db->transCommit();

      return $this->find($this->getInsertID());
    } catch (Exception $e) {
      $this->db->transRollback();
      throw $e;
    }
  }

  /**
   * Acepta una solicitud pendiente y realiza el intercambio de usuarios entre el turno original y el turno propuesto
   *
   * @param int $idSolicitud
   * @param int $idUsuarioSesion
   * @param int $idEmpresaSesion
   * @return array
   * @throws Exception
   */
  public function aceptarSolicitud(int $idSolicitud, int $idUsuarioSesion, int $idEmpresaSesion): array
  {
    $this->db->transBegin();

    try {
      $solicitud = $this->obtenerSolicitudBloqueada($idSolicitud);

      if (!$solicitud) {
        throw new Exception('La solicitud no existe.', 404);
      }

      if ((int) $solicitud['sol_id_usuario_destinatario'] !== $idUsuarioSesion) {
        throw new Exception('No tienes permiso para aceptar esta solicitud.', 403);
      }

      if ($solicitud['sol_estado'] !== 'pendiente') {
        throw new Exception('La solicitud ya no está pendiente.', 409);
      }

      $solicitante = $this->obtenerUsuario((int) $solicitud['sol_id_usuario_solicitante']);
      $destinatario = $this->obtenerUsuario((int) $solicitud['sol_id_usuario_destinatario']);

      if (!$solicitante) {
        throw new Exception('El usuario solicitante no existe.', 404);
      }

      if (!$destinatario) {
        throw new Exception('El usuario destinatario no existe.', 404);
      }

      if ((int) $solicitante['usu_id_empresa'] !== (int) $destinatario['usu_id_empresa']) {
        throw new Exception('Los usuarios no pertenecen a la misma empresa.', 422);
      }

      if ((int) $solicitante['usu_id_empresa'] !== $idEmpresaSesion) {
        throw new Exception('No tienes permiso para aceptar solicitudes de otra empresa.', 403);
      }

      $turnoOriginal = $this->obtenerTurnoBloqueado((int) $solicitud['sol_id_turno_original']);
      $turnoPropuesto = $this->obtenerTurnoBloqueado((int) $solicitud['sol_id_turno_propuesto']);

      if (!$turnoOriginal || !$turnoPropuesto) {
        throw new Exception('Uno de los turnos ya no existe.', 404);
      }

      $this->validarTurnosMismaEmpresa($turnoOriginal, $turnoPropuesto, (int) $solicitante['usu_id_empresa']);

      if ((int) $turnoOriginal['hor_id_empresa'] !== $idEmpresaSesion || (int) $turnoPropuesto['hor_id_empresa'] !== $idEmpresaSesion) {
        throw new Exception('No tienes permiso para aceptar solicitudes con turnos de otra empresa.', 403);
      }

      $this->validarPropiedadTurnos(
        $turnoOriginal,
        $turnoPropuesto,
        (int) $solicitud['sol_id_usuario_solicitante'],
        (int) $solicitud['sol_id_usuario_destinatario']
      );

      if ($turnoOriginal['tur_estado'] === 'cancelado' || $turnoPropuesto['tur_estado'] === 'cancelado') {
        throw new Exception('No se puede aceptar una solicitud con turnos cancelados.', 409);
      }

      $this->validarSolapesIntercambio(
        $turnoOriginal,
        $turnoPropuesto,
        (int) $solicitud['sol_id_usuario_solicitante'],
        (int) $solicitud['sol_id_usuario_destinatario']
      );

      $this->actualizarTurnoUsuarioYEstado(
        (int) $turnoOriginal['tur_id_turno'],
        (int) $solicitud['sol_id_usuario_destinatario'],
        'cambiado'
      );

      $this->actualizarTurnoUsuarioYEstado(
        (int) $turnoPropuesto['tur_id_turno'],
        (int) $solicitud['sol_id_usuario_solicitante'],
        'cambiado'
      );

      $this->update($idSolicitud, [
        'sol_estado' => 'aceptada',
        'sol_fecha_resolucion' => date('Y-m-d H:i:s'),
        'sol_comentario_resolucion' => 'Solicitud aceptada correctamente.',
      ]);

      $this->cancelarSolicitudesPendientesRelacionadas(
        (int) $solicitud['sol_id_solicitud'],
        (int) $solicitud['sol_id_turno_original'],
        (int) $solicitud['sol_id_turno_propuesto']
      );

      $this->db->transCommit();

      return $this->find($idSolicitud);
    } catch (Exception $e) {
      $this->db->transRollback();
      throw $e;
    }
  }

  /**
   * Rechaza una solicitud pendiente y libera los turnos implicados si ya no tienen más solicitudes pendientes
   *
   * @param int $idSolicitud
   * @param int $idUsuarioSesion
   * @param int $idEmpresaSesion
   * @param string|null $comentario
   * @return array
   * @throws Exception
   */
  public function rechazarSolicitud(int $idSolicitud, int $idUsuarioSesion, int $idEmpresaSesion, ?string $comentario = null): array
  {
    $this->db->transBegin();

    try {
      $solicitud = $this->obtenerSolicitudBloqueada($idSolicitud);

      if (!$solicitud) {
        throw new Exception('La solicitud no existe.', 404);
      }

      if ((int) $solicitud['sol_id_usuario_destinatario'] !== $idUsuarioSesion) {
        throw new Exception('No tienes permiso para rechazar esta solicitud.', 403);
      }

      if ($solicitud['sol_estado'] !== 'pendiente') {
        throw new Exception('Solo se pueden rechazar solicitudes pendientes.', 409);
      }

      $solicitante = $this->obtenerUsuario((int) $solicitud['sol_id_usuario_solicitante']);
      $destinatario = $this->obtenerUsuario((int) $solicitud['sol_id_usuario_destinatario']);

      if (!$solicitante) {
        throw new Exception('El usuario solicitante no existe.', 404);
      }

      if (!$destinatario) {
        throw new Exception('El usuario destinatario no existe.', 404);
      }

      if ((int) $solicitante['usu_id_empresa'] !== (int) $destinatario['usu_id_empresa']) {
        throw new Exception('Los usuarios no pertenecen a la misma empresa.', 422);
      }

      if ((int) $solicitante['usu_id_empresa'] !== $idEmpresaSesion) {
        throw new Exception('No tienes permiso para rechazar solicitudes de otra empresa.', 403);
      }

      $turnoOriginal = $this->obtenerTurnoBloqueado((int) $solicitud['sol_id_turno_original']);
      $turnoPropuesto = $this->obtenerTurnoBloqueado((int) $solicitud['sol_id_turno_propuesto']);

      if (!$turnoOriginal || !$turnoPropuesto) {
        throw new Exception('Uno de los turnos ya no existe.', 404);
      }

      $this->validarTurnosMismaEmpresa($turnoOriginal, $turnoPropuesto, (int) $solicitante['usu_id_empresa']);

      if ((int) $turnoOriginal['hor_id_empresa'] !== $idEmpresaSesion || (int) $turnoPropuesto['hor_id_empresa'] !== $idEmpresaSesion) {
        throw new Exception('No tienes permiso para rechazar solicitudes con turnos de otra empresa.', 403);
      }

      $this->update($idSolicitud, [
        'sol_estado' => 'rechazada',
        'sol_fecha_resolucion' => date('Y-m-d H:i:s'),
        'sol_comentario_resolucion' => $comentario,
      ]);

      $this->liberarTurnoSiNoTieneSolicitudesPendientes((int) $solicitud['sol_id_turno_original']);
      $this->liberarTurnoSiNoTieneSolicitudesPendientes((int) $solicitud['sol_id_turno_propuesto']);

      $this->db->transCommit();

      return $this->find($idSolicitud);
    } catch (Exception $e) {
      $this->db->transRollback();
      throw $e;
    }
  }

  /**
   * Cancela una solicitud pendiente y libera los turnos
   *
   * @param int $idSolicitud
   * @param int $idUsuarioSesion
   * @param int $idEmpresaSesion
   * @param string|null $comentario
   * @return array
   * @throws Exception
   */
  public function cancelarSolicitud(int $idSolicitud, int $idUsuarioSesion, int $idEmpresaSesion, ?string $comentario = null): array
  {
    $this->db->transBegin();

    try {
      $solicitud = $this->obtenerSolicitudBloqueada($idSolicitud);

      if (!$solicitud) {
        throw new Exception('La solicitud no existe.', 404);
      }

      if ((int) $solicitud['sol_id_usuario_solicitante'] !== $idUsuarioSesion) {
        throw new Exception('No tienes permiso para cancelar esta solicitud.', 403);
      }

      if ($solicitud['sol_estado'] !== 'pendiente') {
        throw new Exception('Solo se pueden cancelar solicitudes pendientes.', 409);
      }

      $solicitante = $this->obtenerUsuario((int) $solicitud['sol_id_usuario_solicitante']);
      $destinatario = $this->obtenerUsuario((int) $solicitud['sol_id_usuario_destinatario']);

      if (!$solicitante) {
        throw new Exception('El usuario solicitante no existe.', 404);
      }

      if (!$destinatario) {
        throw new Exception('El usuario destinatario no existe.', 404);
      }

      if ((int) $solicitante['usu_id_empresa'] !== (int) $destinatario['usu_id_empresa']) {
        throw new Exception('Los usuarios no pertenecen a la misma empresa.', 422);
      }

      if ((int) $solicitante['usu_id_empresa'] !== $idEmpresaSesion) {
        throw new Exception('No tienes permiso para cancelar solicitudes de otra empresa.', 403);
      }

      $turnoOriginal = $this->obtenerTurnoBloqueado((int) $solicitud['sol_id_turno_original']);
      $turnoPropuesto = $this->obtenerTurnoBloqueado((int) $solicitud['sol_id_turno_propuesto']);

      if (!$turnoOriginal || !$turnoPropuesto) {
        throw new Exception('Uno de los turnos ya no existe.', 404);
      }

      $this->validarTurnosMismaEmpresa($turnoOriginal, $turnoPropuesto, (int) $solicitante['usu_id_empresa']);

      if ((int) $turnoOriginal['hor_id_empresa'] !== $idEmpresaSesion || (int) $turnoPropuesto['hor_id_empresa'] !== $idEmpresaSesion) {
        throw new Exception('No tienes permiso para cancelar solicitudes con turnos de otra empresa.', 403);
      }

      $this->update($idSolicitud, [
        'sol_estado' => 'cancelada',
        'sol_fecha_resolucion' => date('Y-m-d H:i:s'),
        'sol_comentario_resolucion' => $comentario,
      ]);

      $this->liberarTurnoSiNoTieneSolicitudesPendientes((int) $solicitud['sol_id_turno_original']);
      $this->liberarTurnoSiNoTieneSolicitudesPendientes((int) $solicitud['sol_id_turno_propuesto']);

      $this->db->transCommit();

      return $this->find($idSolicitud);
    } catch (Exception $e) {
      $this->db->transRollback();
      throw $e;
    }
  }

  /**
   * Devuelve una solicitud concreta si el usuario autenticado tiene permiso para verla
   *
   * @param int $idSolicitud
   * @param int $idUsuarioSesion
   * @param int $idEmpresaSesion
   * @param string $rolSesion
   * @return array
   * @throws Exception
   */
  public function obtenerSolicitudVisibleParaUsuario(int $idSolicitud, int $idUsuarioSesion, int $idEmpresaSesion, string $rolSesion): array
  {
    $builder = $this->db->table('solicitudes_cambio_turno s')
      ->select('
        s.*,
        us.usu_nombre AS solicitante_nombre,
        us.usu_apellidos AS solicitante_apellidos,
        ud.usu_nombre AS destinatario_nombre,
        ud.usu_apellidos AS destinatario_apellidos,
        t1.tur_inicio AS turno_original_inicio,
        t1.tur_fin AS turno_original_fin,
        t1.tur_estado AS turno_original_estado,
        t1.tur_observaciones AS turno_original_observaciones,
        t2.tur_inicio AS turno_propuesto_inicio,
        t2.tur_fin AS turno_propuesto_fin,
        t2.tur_estado AS turno_propuesto_estado,
        t2.tur_observaciones AS turno_propuesto_observaciones
      ')
      ->join('usuarios us', 'us.usu_id_usuario = s.sol_id_usuario_solicitante')
      ->join('usuarios ud', 'ud.usu_id_usuario = s.sol_id_usuario_destinatario')
      ->join('turnos t1', 't1.tur_id_turno = s.sol_id_turno_original')
      ->join('turnos t2', 't2.tur_id_turno = s.sol_id_turno_propuesto')
      ->where('s.sol_id_solicitud', $idSolicitud)
      ->where('us.usu_id_empresa', $idEmpresaSesion)
      ->where('ud.usu_id_empresa', $idEmpresaSesion);

    if ($rolSesion !== 'administrador') {
      $builder->groupStart()
        ->where('s.sol_id_usuario_solicitante', $idUsuarioSesion)
        ->orWhere('s.sol_id_usuario_destinatario', $idUsuarioSesion)
        ->groupEnd();
    }

    $solicitud = $builder->get()->getRowArray();

    if (!$solicitud) {
      throw new Exception('No tienes permiso para ver esta solicitud o no existe.', 404);
    }

    return $solicitud;
  }

  /**
   * Devuelve las solicitudes visibles para el usuario autenticado según su rol y su empresa
   *
   * @param int $idUsuarioSesion
   * @param int $idEmpresaSesion
   * @param string $rolSesion
   * @param array $filtros
   * @return array
   */
  public function obtenerSolicitudesVisiblesParaUsuario(int $idUsuarioSesion, int $idEmpresaSesion, string $rolSesion, array $filtros = []): array
  {
    $builder = $this->db->table('solicitudes_cambio_turno s')
      ->select('
        s.*,
        us.usu_nombre AS solicitante_nombre,
        us.usu_apellidos AS solicitante_apellidos,
        ud.usu_nombre AS destinatario_nombre,
        ud.usu_apellidos AS destinatario_apellidos,
        t1.tur_inicio AS turno_original_inicio,
        t1.tur_fin AS turno_original_fin,
        t1.tur_estado AS turno_original_estado,
        t1.tur_observaciones AS turno_original_observaciones,
        t2.tur_inicio AS turno_propuesto_inicio,
        t2.tur_fin AS turno_propuesto_fin,
        t2.tur_estado AS turno_propuesto_estado,
        t2.tur_observaciones AS turno_propuesto_observaciones
      ')
      ->join('usuarios us', 'us.usu_id_usuario = s.sol_id_usuario_solicitante')
      ->join('usuarios ud', 'ud.usu_id_usuario = s.sol_id_usuario_destinatario')
      ->join('turnos t1', 't1.tur_id_turno = s.sol_id_turno_original')
      ->join('turnos t2', 't2.tur_id_turno = s.sol_id_turno_propuesto')
      ->where('us.usu_id_empresa', $idEmpresaSesion)
      ->where('ud.usu_id_empresa', $idEmpresaSesion);

    if ($rolSesion !== 'administrador') {
      $builder->groupStart()
        ->where('s.sol_id_usuario_solicitante', $idUsuarioSesion)
        ->orWhere('s.sol_id_usuario_destinatario', $idUsuarioSesion)
        ->groupEnd();
    }

    $this->aplicarFiltrosListado($builder, $filtros, $rolSesion, $idUsuarioSesion);

    return $builder
      ->orderBy('s.sol_fecha_solicitud', 'DESC')
      ->get()
      ->getResultArray();
  }

  /**
   * Obtiene una solicitud por id y la bloquea para que no la pueda modificar el otro usuario a la vez
   *
   * @param int $idSolicitud
   * @return array|null
   */
  private function obtenerSolicitudBloqueada(int $idSolicitud): ?array
  {
    $sql = '
            SELECT *
            FROM solicitudes_cambio_turno
            WHERE sol_id_solicitud = ?
            FOR UPDATE
        ';

    $fila = $this->db->query($sql, [$idSolicitud])->getRowArray();

    return $fila ?: null;
  }

  /**
   * Obtiene un usuario por su id
   *
   * @param int $idUsuario
   * @return array|null
   */
  private function obtenerUsuario(int $idUsuario): ?array
  {
    $fila = $this->db->table('usuarios')
      ->where('usu_id_usuario', $idUsuario)
      ->get()
      ->getRowArray();

    return $fila ?: null;
  }

  /**
   * Obtiene un turno por id y lo bloquea para que no se pueda usar en otro intercambio
   * Obtiene la empresa por medio del horario
   *
   * @param int $idTurno
   * @return array|null
   */
  private function obtenerTurnoBloqueado(int $idTurno): ?array
  {
    $sql = '
            SELECT
                t.*,
                h.hor_id_empresa
            FROM turnos t
            INNER JOIN horarios h
                ON h.hor_id_horario = t.tur_id_horario
            WHERE t.tur_id_turno = ?
            FOR UPDATE
        ';

    $fila = $this->db->query($sql, [$idTurno])->getRowArray();

    return $fila ?: null;
  }

  /**
   * Comprueba que los turnos y los usuarios pertenecen a la misma empresa
   *
   * @param array $turnoOriginal
   * @param array $turnoPropuesto
   * @param int $idEmpresa
   * @return void
   * @throws Exception
   */
  private function validarTurnosMismaEmpresa(array $turnoOriginal, array $turnoPropuesto, int $idEmpresa): void
  {
    if ((int) $turnoOriginal['hor_id_empresa'] !== $idEmpresa) {
      throw new Exception('El turno original no pertenece a la empresa del usuario solicitante.', 422);
    }

    if ((int) $turnoPropuesto['hor_id_empresa'] !== $idEmpresa) {
      throw new Exception('El turno propuesto no pertenece a la empresa del usuario solicitante.', 422);
    }

    if ((int) $turnoOriginal['hor_id_empresa'] !== (int) $turnoPropuesto['hor_id_empresa']) {
      throw new Exception('Los turnos no pertenecen a la misma empresa.', 422);
    }
  }

  /**
   * Comprueba que cada turno sigue perteneciendo al usuario correcto
   *
   * @param array $turnoOriginal
   * @param array $turnoPropuesto
   * @param int $idSolicitante
   * @param int $idDestinatario
   * @return void
   * @throws Exception
   */
  private function validarPropiedadTurnos(
    array $turnoOriginal,
    array $turnoPropuesto,
    int $idSolicitante,
    int $idDestinatario
  ): void {
    if ((int) $turnoOriginal['tur_id_usuario'] !== $idSolicitante) {
      throw new Exception('El turno original ya no pertenece al usuario solicitante.', 409);
    }

    if ((int) $turnoPropuesto['tur_id_usuario'] !== $idDestinatario) {
      throw new Exception('El turno propuesto ya no pertenece al usuario destinatario.', 409);
    }
  }

  /**
   * Comprueba que los dos turnos están en un estado adecuado para crer la solicitud
   *
   * @param array $turnoOriginal
   * @param array $turnoPropuesto
   * @return void
   * @throws Exception
   */
  private function validarEstadosTurnosParaSolicitud(array $turnoOriginal, array $turnoPropuesto): void
  {
    $estadosNoPermitidos = ['cancelado', 'cambiado'];

    if (in_array($turnoOriginal['tur_estado'], $estadosNoPermitidos, true)) {
      throw new Exception('El turno original no está disponible para intercambio.', 409);
    }

    if (in_array($turnoPropuesto['tur_estado'], $estadosNoPermitidos, true)) {
      throw new Exception('El turno propuesto no está disponible para intercambio.', 409);
    }

    if ($turnoOriginal['tur_estado'] === 'pendiente_cambio' || $turnoPropuesto['tur_estado'] === 'pendiente_cambio') {
      throw new Exception('Uno de los turnos ya tiene una solicitud pendiente.', 409);
    }
  }

  /**
   * Comprueba si existe otra solicitud pendiente relacionada con alguno de los turnos a cambiar
   *
   * @param int $idTurnoOriginal
   * @param int $idTurnoPropuesto
   * @return bool
   */
  private function existeSolicitudPendienteRelacionada(int $idTurnoOriginal, int $idTurnoPropuesto): bool
  {
    $fila = $this->db->table('solicitudes_cambio_turno')
      ->groupStart()
      ->where('sol_id_turno_original', $idTurnoOriginal)
      ->orWhere('sol_id_turno_original', $idTurnoPropuesto)
      ->orWhere('sol_id_turno_propuesto', $idTurnoOriginal)
      ->orWhere('sol_id_turno_propuesto', $idTurnoPropuesto)
      ->groupEnd()
      ->where('sol_estado', 'pendiente')
      ->limit(1)
      ->get()
      ->getRowArray();

    return !empty($fila);
  }

  /**
   * Comprueba si tras el intercambio a alguno de los 2 usuarios se le pisaría un turno con otro
   *
   * @param array $turnoOriginal
   * @param array $turnoPropuesto
   * @param int $idSolicitante
   * @param int $idDestinatario
   * @return void
   * @throws Exception
   */
  private function validarSolapesIntercambio(array $turnoOriginal, array $turnoPropuesto, int $idSolicitante, int $idDestinatario): void
  {
    $solapeSolicitante = $this->existeSolapeParaUsuario(
      $idSolicitante,
      (int) $turnoOriginal['tur_id_turno'],
      (int) $turnoPropuesto['tur_id_turno'],
      $turnoPropuesto['tur_inicio'],
      $turnoPropuesto['tur_fin']
    );

    if ($solapeSolicitante) {
      throw new Exception('El usuario solicitante tendría un solape con el turno propuesto.', 409);
    }

    $solapeDestinatario = $this->existeSolapeParaUsuario(
      $idDestinatario,
      (int) $turnoPropuesto['tur_id_turno'],
      (int) $turnoOriginal['tur_id_turno'],
      $turnoOriginal['tur_inicio'],
      $turnoOriginal['tur_fin']
    );

    if ($solapeDestinatario) {
      throw new Exception('El usuario destinatario tendría un solape con el turno original.', 409);
    }
  }

  /**
   * Comprueba que un turno del usuario no se solapa con un turno concreto
   *
   * @param int $idUsuario
   * @param int $idTurnoActualUsuario
   * @param int $idTurnoIntercambio
   * @param string $inicio
   * @param string $fin
   * @return bool
   */
  private function existeSolapeParaUsuario(int $idUsuario, int $idTurnoActualUsuario, int $idTurnoIntercambio, string $inicio, string $fin): bool
  {
    $sql = '
            SELECT tur_id_turno
            FROM turnos
            WHERE tur_id_usuario = ?
              AND tur_estado <> "cancelado"
              AND tur_id_turno NOT IN (?, ?)
              AND tur_inicio < ?
              AND tur_fin > ?
            LIMIT 1
        ';

    $fila = $this->db->query($sql, [
      $idUsuario,
      $idTurnoActualUsuario,
      $idTurnoIntercambio,
      $fin,
      $inicio,
    ])->getRowArray();

    return !empty($fila);
  }

  /**
   * Actualiza solo el estado del turno.
   *
   * @param int $idTurno
   * @param string $estado
   * @return void
   */
  private function actualizarEstadoTurno(int $idTurno, string $estado): void
  {
    $this->db->table('turnos')
      ->where('tur_id_turno', $idTurno)
      ->update([
        'tur_estado' => $estado,
      ]);
  }

  /**
   * Actualiza el usuario asignado y el estado del turno
   *
   * @param int $idTurno
   * @param int $idUsuario
   * @param string $estado
   * @return void
   */
  private function actualizarTurnoUsuarioYEstado(int $idTurno, int $idUsuario, string $estado): void
  {
    $this->db->table('turnos')
      ->where('tur_id_turno', $idTurno)
      ->update([
        'tur_id_usuario' => $idUsuario,
        'tur_estado' => $estado,
      ]);
  }

  /**
   * Cancela otras solicitudes pendientes que tengan relación con esos turnos
   * @param int $idSolicitudAceptada
   * @param int $idTurnoOriginal
   * @param int $idTurnoPropuesto
   * @return void
   */
  private function cancelarSolicitudesPendientesRelacionadas(int $idSolicitudAceptada, int $idTurnoOriginal, int $idTurnoPropuesto): void
  {
    $this->db->table('solicitudes_cambio_turno')
      ->where('sol_estado', 'pendiente')
      ->where('sol_id_solicitud <>', $idSolicitudAceptada)
      ->groupStart()
      ->whereIn('sol_id_turno_original', [$idTurnoOriginal, $idTurnoPropuesto])
      ->orWhereIn('sol_id_turno_propuesto', [$idTurnoOriginal, $idTurnoPropuesto])
      ->groupEnd()
      ->update([
        'sol_estado' => 'cancelada',
        'sol_fecha_resolucion' => date('Y-m-d H:i:s'),
        'sol_comentario_resolucion' => 'Cancelada automáticamente por aceptación de otra solicitud relacionada.',
      ]);
  }

  /**
   * Si un turno ya no tiene solicitudes pendientes lo devuelve al estado asignado
   *
   * @param int $idTurno
   * @return void
   */
  private function liberarTurnoSiNoTieneSolicitudesPendientes(int $idTurno): void
  {
    $pendiente = $this->db->table('solicitudes_cambio_turno')
      ->groupStart()
      ->where('sol_id_turno_original', $idTurno)
      ->orWhere('sol_id_turno_propuesto', $idTurno)
      ->groupEnd()
      ->where('sol_estado', 'pendiente')
      ->limit(1)
      ->get()
      ->getRowArray();

    if (!$pendiente) {
      $this->actualizarEstadoTurno($idTurno, 'asignado');
    }
  }

  /**
   * Normaliza y valida los datos de creación de solicitud
   *
   * @param array $datos
   * @return array
   * @throws Exception
   */
  private function normalizarDatosCreacion(array $datos): array
  {
    $normalizados = [
      'sol_id_usuario_solicitante' => isset($datos['sol_id_usuario_solicitante']) ? (int) $datos['sol_id_usuario_solicitante'] : 0,
      'sol_id_usuario_destinatario' => isset($datos['sol_id_usuario_destinatario']) ? (int) $datos['sol_id_usuario_destinatario'] : 0,
      'sol_id_turno_original' => isset($datos['sol_id_turno_original']) ? (int) $datos['sol_id_turno_original'] : 0,
      'sol_id_turno_propuesto' => isset($datos['sol_id_turno_propuesto']) ? (int) $datos['sol_id_turno_propuesto'] : 0,
      'sol_motivo' => isset($datos['sol_motivo']) ? trim((string) $datos['sol_motivo']) : null,
    ];

    if ($normalizados['sol_id_usuario_solicitante'] <= 0) {
      throw new Exception('El usuario solicitante es obligatorio.', 422);
    }

    if ($normalizados['sol_id_usuario_destinatario'] <= 0) {
      throw new Exception('El usuario destinatario es obligatorio.', 422);
    }

    if ($normalizados['sol_id_turno_original'] <= 0) {
      throw new Exception('El turno original es obligatorio.', 422);
    }

    if ($normalizados['sol_id_turno_propuesto'] <= 0) {
      throw new Exception('El turno propuesto es obligatorio.', 422);
    }

    if ($normalizados['sol_id_turno_original'] === $normalizados['sol_id_turno_propuesto']) {
      throw new Exception('El turno original y el turno propuesto no pueden ser el mismo.', 422);
    }

    return $normalizados;
  }

  /**
   * Aplica los filtros del listado de solicitudes
   *
   * @param object $builder
   * @param array $filtros
   * @param string $rolSesion
   * @param int $idUsuarioSesion
   * @return void
   * @throws Exception
   */
  private function aplicarFiltrosListado($builder, array $filtros, string $rolSesion, int $idUsuarioSesion): void
  {
    $solicitante = $filtros['sol_id_usuario_solicitante'] ?? null;
    $destinatario = $filtros['sol_id_usuario_destinatario'] ?? null;
    $estado = $filtros['sol_estado'] ?? null;

    if ($rolSesion !== 'administrador') {
      $this->validarFiltrosEmpleado($solicitante, $destinatario, $idUsuarioSesion);
    }

    if ($solicitante !== null && $solicitante !== '') {
      $builder->where('s.sol_id_usuario_solicitante', (int) $solicitante);
    }

    if ($destinatario !== null && $destinatario !== '') {
      $builder->where('s.sol_id_usuario_destinatario', (int) $destinatario);
    }

    if ($estado !== null && $estado !== '') {
      $builder->where('s.sol_estado', $estado);
    }
  }

  /**
   * Impide que un empleado filtre por usuarios ajenos a sus solicitudes
   *
   * @param mixed $solicitante
   * @param mixed $destinatario
   * @param int $idUsuarioSesion
   * @return void
   * @throws Exception
   */
  private function validarFiltrosEmpleado($solicitante, $destinatario, int $idUsuarioSesion): void
  {
    if ($solicitante !== null && $solicitante !== '' && (int) $solicitante !== $idUsuarioSesion) {
      throw new Exception('No tienes permiso para filtrar por otro usuario solicitante.', 403);
    }

    if ($destinatario !== null && $destinatario !== '' && (int) $destinatario !== $idUsuarioSesion) {
      throw new Exception('No tienes permiso para filtrar por otro usuario destinatario.', 403);
    }
  }

    /**
   * Cuenta las solicitudes no vistas del usuario destinatario
   * @param int $idUsuario
   * @return int
   */
  public function contarNoVistas(int $idUsuario): int
  {
      return $this->db->table('solicitudes_cambio_turno')
          ->where('sol_id_usuario_destinatario', $idUsuario)
          ->where('sol_visto', 0)
          ->countAllResults();
  }

  /**
   * Marca como vistas todas las solicitudes donde el usuario es destinatario
   * @param int $idUsuario
   * @return void
   */
  public function marcarTodasComoVistas(int $idUsuario): void
  {
      $this->db->table('solicitudes_cambio_turno')
          ->where('sol_id_usuario_destinatario', $idUsuario)
          ->where('sol_visto', 0)
          ->update(['sol_visto' => 1]);
  }
}
