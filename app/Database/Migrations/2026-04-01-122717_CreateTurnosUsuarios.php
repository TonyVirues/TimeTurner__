<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTurnosUsuarios extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'turUsu_id_turno' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => true,
				'null' => false,
			],
			'turUsu_id_usuario' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => true,
				'null' => false,
			],
		]);

		$this->forge->addKey(['turUsu_id_turno', 'turUsu_id_usuario'], true);
		$this->forge->addKey('turUsu_id_turno');
		$this->forge->addKey('turUsu_id_usuario');

		$this->forge->addForeignKey(
			'turUsu_id_turno',
			'turnos',
			'tur_id_turno',
			'CASCADE',
			'CASCADE',
			'fk_turUsu_turno'
		);

		$this->forge->addForeignKey(
			'turUsu_id_usuario',
			'usuarios',
			'usu_id_usuario',
			'CASCADE',
			'CASCADE',
			'fk_turUsu_usuario'
		);

		$this->forge->createTable('turnos_usuarios');
	}

	public function down()
	{
		$this->forge->dropTable('turnos_usuarios', true);
	}
}
