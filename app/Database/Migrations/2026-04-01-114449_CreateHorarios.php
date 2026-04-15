<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateHorarios extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'hor_id_horario' => [
        'type' => 'INT',
        'constraint' => 10,
        'unsigned' => true,
        'auto_increment' => true,
      ],
      'hor_id_empresa' => [
        'type' => 'INT',
        'constraint' => 10,
        'unsigned' => true,
        'null' => false,
      ],
      'hor_nombre' => [
        'type' => 'VARCHAR',
        'constraint' => 150,
      ],
      'hor_fecha_inicio' => [
        'type' => 'DATE',
        'null' => false,
      ],
      'hor_fecha_fin' => [
        'type' => 'DATE',
        'null' => false,
      ],
      'hor_descripcion' => [
        'type' => 'TEXT',
        'null' => true,
      ],
      'hor_estado' => [
        'type' => 'ENUM',
        'constraint' => ['borrador', 'publicado', 'cerrado'],
        'default' => 'borrador',
      ],
      'hor_creado' => [
        'type' => 'TIMESTAMP',
        'null'    => false,
        'default' => new RawSql('CURRENT_TIMESTAMP'),
      ],
      'hor_actualizado' => [
        'type' => 'TIMESTAMP',
        'null'    => false,
        'default' => new RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
      ],
    ]);

    $this->forge->addKey('hor_id_horario', true);
    $this->forge->addKey('hor_id_empresa');

    $this->forge->addForeignKey(
      'hor_id_empresa',
      'empresas',
      'emp_id_empresa',
      'CASCADE',
      'CASCADE',
      'fk_horarios_empresa'
    );

    $this->forge->createTable('horarios');
  }

  public function down()
  {
    $this->forge->dropTable('horarios', true);
  }
}
