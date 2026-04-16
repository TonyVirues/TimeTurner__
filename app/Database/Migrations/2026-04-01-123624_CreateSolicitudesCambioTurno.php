<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateSolicitudesCambioTurno extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'sol_id_solicitud' => [
        'type' => 'INT',
        'constraint' => 10,
        'unsigned' => true,
        'auto_increment' => true,
      ],
      'sol_id_usuario_solicitante' => [
        'type' => 'INT',
        'constraint' => 10,
        'unsigned' => true,
        'null' => false,
      ],
      'sol_id_usuario_destinatario' => [
        'type' => 'INT',
        'constraint' => 10,
        'unsigned' => true,
        'null' => false,
      ],
      'sol_id_turno_original' => [
        'type' => 'INT',
        'constraint' => 10,
        'unsigned' => true,
        'null' => false,
      ],
      'sol_id_turno_propuesto' => [
        'type' => 'INT',
        'constraint' => 10,
        'unsigned' => true,
        'null' => false,
      ],
      'sol_motivo' => [
        'type' => 'TEXT',
        'null' => true,
      ],
      'sol_estado' => [
        'type' => 'ENUM',
        'constraint' => ['pendiente', 'aceptada', 'rechazada', 'cancelada'],
        'default' => 'pendiente',
      ],
      'sol_fecha_solicitud' => [
        'type' => 'TIMESTAMP',
        'null' => false,
        'default' => new RawSql('CURRENT_TIMESTAMP'),
      ],
      'sol_fecha_resolucion' => [
        'type' => 'DATETIME',
        'null' => true,
      ],
      'sol_comentario_resolucion' => [
        'type' => 'TEXT',
        'null' => true,
      ],
    ]);

    $this->forge->addKey('sol_id_solicitud', true);
    $this->forge->addKey('sol_id_usuario_solicitante');
    $this->forge->addKey('sol_id_usuario_destinatario');
    $this->forge->addKey('sol_id_turno_original');
    $this->forge->addKey('sol_id_turno_propuesto');

    $this->forge->addForeignKey(
      'sol_id_usuario_solicitante',
      'usuarios',
      'usu_id_usuario',
      'CASCADE',
      'CASCADE',
      'fk_sol_usuario_solicitante'
    );

    $this->forge->addForeignKey(
      'sol_id_usuario_destinatario',
      'usuarios',
      'usu_id_usuario',
      'CASCADE',
      'CASCADE',
      'fk_sol_usuario_destinatario'
    );

    $this->forge->addForeignKey(
      'sol_id_turno_original',
      'turnos',
      'tur_id_turno',
      'CASCADE',
      'CASCADE',
      'fk_sol_turno_original'
    );

    $this->forge->addForeignKey(
      'sol_id_turno_propuesto',
      'turnos',
      'tur_id_turno',
      'CASCADE',
      'CASCADE',
      'fk_sol_turno_propuesto'
    );

    $this->forge->createTable('solicitudes_cambio_turno');
  }

  public function down()
  {
    $this->forge->dropTable('solicitudes_cambio_turno', true);
  }
}
