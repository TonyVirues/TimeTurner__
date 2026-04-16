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
}
