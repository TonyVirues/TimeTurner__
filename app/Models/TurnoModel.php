<?php

namespace App\Models;

use CodeIgniter\Model;

class TurnoModel extends Model
{
  protected $table = 'turnos';
  protected $primaryKey = 'tur_id_turno';
  protected $returnType = 'array';
  protected $useAutoIncrement = true;

  /**
   * Campos que se pueden editar desde la web
   * @var array
   */
  protected $allowedFields = [
    'tur_id_horario',
    'tur_id_usuario',
    'tur_inicio',
    'tur_fin',
    'tur_estado',
    'tur_observaciones',
  ];

  /**
   * La fecha y hora automática se gestiona en la db directamente
   * @var bool
   */
  protected $useTimestamps = false;

  /**
   * Llama a un turno concreto por id
   * @param int $id
   * @return array<bool|float|int|object|string|null>|object|null
   */
  public function getTurno(int $id)
  {
    return $this->find($id);
  }

  /**
   * Llama a la lista de turnos de un horario concreto ordenados por inicio
   * @param int $horarioId
   * @return array<array<bool|float|int|object|string|null>|object>
   */
  public function getTurnosPorHorario(int $horarioId)
  {
    return $this->where('tur_id_horario', $horarioId)
      ->orderBy('tur_inicio', 'ASC')
      ->findAll();
  }

  /**
   * Devuelve los turnos de un horario junto al nombre del usuario asignado si existe
   * @param int $horarioId
   * @return array
   */
  public function getTurnosConUsuarioPorHorario(int $horarioId): array
  {
    return $this->select('
        turnos.tur_id_turno,
        turnos.tur_id_horario,
        turnos.tur_id_usuario,
        turnos.tur_inicio,
        turnos.tur_fin,
        turnos.tur_estado,
        turnos.tur_observaciones,
        usuarios.usu_nombre,
        usuarios.usu_apellidos
      ')
      ->join('usuarios', 'usuarios.usu_id_usuario = turnos.tur_id_usuario', 'left')
      ->where('turnos.tur_id_horario', $horarioId)
      ->orderBy('turnos.tur_inicio', 'ASC')
      ->findAll();
  }

  /**
   * Devuelve los turnos de un horario en formato compatible con FullCalendar
   * @param int $horarioId
   * @return array<int, array<string, mixed>>
   */
  public function getTurnosParaCalendario(int $horarioId): array
  {
    $turnos = $this->getTurnosConUsuarioPorHorario($horarioId);

    $eventos = [];

    foreach ($turnos as $turno) {
      $nombreCompleto = trim(($turno['usu_nombre'] ?? '') . ' ' . ($turno['usu_apellidos'] ?? ''));

      $eventos[] = [
        'id' => $turno['tur_id_turno'],
        'title' => $nombreCompleto !== '' ? $nombreCompleto : 'Sin asignar',
        'start' => date('c', strtotime($turno['tur_inicio'])),
        'end' => date('c', strtotime($turno['tur_fin'])),
        'extendedProps' => [
          'estado' => $turno['tur_estado'],
          'usuario' => $nombreCompleto !== '' ? $nombreCompleto : null,
          'tur_id_usuario' => $turno['tur_id_usuario'],
          'observaciones' => $turno['tur_observaciones'],
          'tur_id_horario' => $turno['tur_id_horario'],
        ],
      ];
    }

    return $eventos;
  }

  /**
   * Comprueba si un usuario ya tiene otro turno que se solapa con el rango indicado
   * @param int $usuarioId
   * @param string $inicio
   * @param string $fin
   * @param int|null $turnoIdExcluir
   * @return bool
   */
  public function existeSolapeUsuario(
    int $usuarioId,
    string $inicio,
    string $fin,
    ?int $turnoIdExcluir = null
  ): bool {
    $builder = $this->where('tur_id_usuario', $usuarioId)
      ->where('tur_inicio <', $fin)
      ->where('tur_fin >', $inicio);

    if ($turnoIdExcluir !== null) {
      $builder->where('tur_id_turno !=', $turnoIdExcluir);
    }

    return $builder->countAllResults() > 0;
  }

  /**
 * Devuelve los turnos asignados al usuario indicado
 * @param int $usuarioId
 * @return array
 */
public function getTurnosPorUsuario(int $usuarioId): array{
    return $this->where('tur_id_usuario', $usuarioId)
        ->whereIn('tur_estado', ['asignado', 'pendiente_cambio'])
        ->orderBy('tur_inicio', 'ASC')
        ->findAll();
}

/**
 * Devuelve los turnos asignados a un usuario concreto de la misma empresa
 * @param int $usuarioId
 * @param int $idEmpresa
 * @return array
 */
public function getTurnosPorUsuarioYEmpresa(int $usuarioId, int $idEmpresa): array{
    return $this->select('turnos.*')
        ->join('horarios', 'horarios.hor_id_horario = turnos.tur_id_horario')
        ->where('turnos.tur_id_usuario', $usuarioId)
        ->where('horarios.hor_id_empresa', $idEmpresa)
        ->whereIn('turnos.tur_estado', ['asignado', 'pendiente_cambio'])
        ->orderBy('turnos.tur_inicio', 'ASC')
        ->findAll();
}


}
