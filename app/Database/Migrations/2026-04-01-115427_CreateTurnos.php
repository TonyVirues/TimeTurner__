<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateTurnos extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'tur_id_turno' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'tur_id_horario' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => true,
				'null' => false,
			],
			'tur_inicio' => [
				'type' => 'DATETIME',
				'null' => false,
			],
			'tur_fin' => [
				'type' => 'DATETIME',
				'null' => false,
			],
			'tur_estado' => [
				'type' => 'ENUM',
				'constraint' => ['asignado', 'disponible', 'pendiente_cambio', 'cambiado', 'cancelado'],
				'default' => 'disponible',
			],
			'tur_observaciones' => [
				'type' => 'TEXT',
				'null' => true,
			],
			'tur_creado' => [
				'type' => 'TIMESTAMP',
				'null' => false,
				'default' => new RawSql('CURRENT_TIMESTAMP'),
			],
			'tur_actualizado' => [
				'type' => 'TIMESTAMP',
				'null' => false,
				'default' => new RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
			],
		]);

		$this->forge->addKey('tur_id_turno', true);
		$this->forge->addKey('tur_id_horario');
		$this->forge->addForeignKey(
			'tur_id_horario',
			'horarios',
			'hor_id_horario',
			'CASCADE',
			'CASCADE',
			'fk_turnos_horario'
		);

		$this->forge->createTable('turnos');
	}

	public function down()
	{
		$this->forge->dropTable('turnos', true);
	}
}
