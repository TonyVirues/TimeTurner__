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
   * Devuelve los turnos de un horario en formato compatible con FullCalendar
   * @param int $horarioId
   * @return array<int, array<string, int|string|null>>
   */
  public function getTurnosParaCalendario(int $horarioId): array
  {
    $turnos = $this->getTurnosPorHorario($horarioId);

    $eventos = [];

    foreach ($turnos as $turno) {
      $eventos[] = [
        'id' => $turno['tur_id_turno'],
        'title' => date('H:i', strtotime($turno['tur_inicio'])) . ' - ' . date('H:i', strtotime($turno['tur_fin'])),
        'start' => date('c', strtotime($turno['tur_inicio'])),
        'end' => date('c', strtotime($turno['tur_fin'])),
        'extendedProps' => [
          'tur_id_horario' => $turno['tur_id_horario'],
          'tur_estado' => $turno['tur_estado'],
          'tur_observaciones' => $turno['tur_observaciones'],
        ],
      ];
    }

    return $eventos;
  }
}
