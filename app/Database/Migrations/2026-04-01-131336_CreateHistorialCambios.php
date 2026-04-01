<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateHistorialCambios extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'his_id_historial' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'his_id_usuario' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => true,
				'null' => false,
			],
			'his_tipo_cambio' => [
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => false,
			],
			'his_descripcion' => [
				'type' => 'TEXT',
				'null' => false,
			],
			'his_fecha_cambio' => [
				'type' => 'TIMESTAMP',
				'null' => false,
				'default' => new RawSql('CURRENT_TIMESTAMP'),
			],
			'his_id_usuario_responsable' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => true,
				'null' => true,
			],
			'his_entidad_afectada' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
				'null' => false,
			],
			'his_id_entidad_afectada' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => true,
				'null' => false,
			],
		]);

		$this->forge->addKey('his_id_historial', true);
		$this->forge->addKey('his_id_usuario');
		$this->forge->addKey('his_id_usuario_responsable');
		$this->forge->addKey('his_entidad_afectada');
		$this->forge->addKey('his_id_entidad_afectada');

		$this->forge->addForeignKey(
			'his_id_usuario',
			'usuarios',
			'usu_id_usuario',
			'CASCADE',
			'CASCADE',
			'fk_his_usuario'
		);

		$this->forge->addForeignKey(
			'his_id_usuario_responsable',
			'usuarios',
			'usu_id_usuario',
			'SET NULL',
			'CASCADE',
			'fk_his_usuario_responsable'
		);

		$this->forge->createTable('historial_cambios');
	}

	public function down()
	{
		$this->forge->dropTable('historial_cambios', true);
	}
}
