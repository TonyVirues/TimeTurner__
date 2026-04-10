<?php

namespace App\Models;

use CodeIgniter\Model;

class HorarioModel extends Model
{
  protected $table = 'horarios';
  protected $primaryKey = 'hor_id_horario';
  protected $returnType = 'array';
  protected $useAutoIncrement = true;

  /**
   * Campos que se pueden editar desde la web
   * @var array
   */
  protected $allowedFields = [
    'hor_nombre',
    'hor_fecha_inicio',
    'hor_fecha_fin',
    'hor_descripcion',
    'hor_estado',
  ];

  /**
   * La fecha y hora automática se gestiona en la db directamente
   * @var bool
   */
  protected $useTimestamps = false;

  /**
   * Llama a la lista de horarios COMPLETOS ordenados por fecha de inicio
   * @return array<array<bool|float|int|object|string|null>|object>
   */
  public function getHorarios()
  {
    return $this->orderBy('hor_fecha_inicio', 'ASC')->findAll();
  }

  /**
   * Llama a un horario concreto por id
   * @param int $id
   * @return array<bool|float|int|object|string|null>|object|null
   */
  public function getHorario(int $id)
  {
    return $this->find($id);
  }

  // Listado de horarios para el selector de horarios
  public function getHorariosListado()
  {
    return $this->select('hor_id_horario, hor_nombre, hor_estado, hor_fecha_inicio, hor_fecha_fin')
      ->orderBy('hor_fecha_inicio', 'ASC')
      ->findAll();
  }
}
