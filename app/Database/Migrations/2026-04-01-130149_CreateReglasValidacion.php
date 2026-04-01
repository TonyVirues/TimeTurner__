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
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => false,
			],
			'reg_valor' => [
				'type' => 'INT',
				'constraint' => 11,
				'null' => false,
			],
			'reg_unidad' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
				'null' => false,
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
