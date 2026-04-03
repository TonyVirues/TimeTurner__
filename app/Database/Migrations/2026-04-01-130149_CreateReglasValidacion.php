<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateReglasValidacion extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'reg_id_regla' => [
        'type' => 'INT',
        'constraint' => 10,
        'unsigned' => true,
        'auto_increment' => true,
      ],
      'reg_id_administrador' => [
        'type' => 'INT',
        'constraint' => 10,
        'unsigned' => true,
        'null' => true,
      ],
      'reg_nombre' => [
        'type' => 'VARCHAR',
        'constraint' => 150,
        'null' => false,
      ],
      'reg_descripcion' => [
        'type' => 'TEXT',
        'null' => true,
      ],
      'reg_tipo_regla' => [
        'type' => 'ENUM',
        'constraint' => [
          'max_horas_consecutivas',
          'min_horas_descanso',
          'max_horas_dia',
          'max_horas_semana',
          'max_horas_mes',
          'max_turnos_dia',
          'min_dias_descanso_semana',
          'max_dias_trabajo_consecutivos'
        ],
        'null' => false,
      ],
      'reg_valor' => [
        'type' => 'INT',
        'constraint' => 11,
        'null' => false,
      ],
      'reg_unidad' => [
        'type' => 'ENUM',
        'constraint' => [
          'horas',
          'dias',
          'turnos'
        ],
        'null' => false,
        'default' => 'horas',
      ],
      'reg_activa' => [
        'type' => 'TINYINT',
        'constraint' => 1,
        'default' => 1,
      ],
      'reg_creada' => [
        'type' => 'TIMESTAMP',
        'null' => false,
        'default' => new RawSql('CURRENT_TIMESTAMP'),
      ],
      'reg_actualizada' => [
        'type' => 'TIMESTAMP',
        'null' => false,
        'default' => new RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
      ],
    ]);

    $this->forge->addKey('reg_id_regla', true);
    $this->forge->addKey('reg_id_administrador');

    $this->forge->addForeignKey(
      'reg_id_administrador',
      'usuarios',
      'usu_id_usuario',
      'SET NULL',
      'CASCADE',
      'fk_reg_administrador'
    );

    $this->forge->createTable('reglas_validacion');
  }

  public function down()
  {
    $this->forge->dropTable('reglas_validacion', true);
  }
}
