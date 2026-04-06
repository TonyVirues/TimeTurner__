<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateMensajesChat extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'sms_id_mensaje' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'sms_id_emisor' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => true,
				'null' => true,
			],
			'sms_id_receptor' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => true,
				'null' => true,
			],
			'sms_contenido' => [
				'type' => 'TEXT',
				'null' => false,
			],
			'sms_leido' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 0,
			],
			'sms_fecha_envio' => [
				'type' => 'TIMESTAMP',
				'null' => false,
				'default' => new RawSql('CURRENT_TIMESTAMP'),
			],
		]);

		$this->forge->addKey('sms_id_mensaje', true);
		$this->forge->addKey('sms_id_emisor');
		$this->forge->addKey('sms_id_receptor');

		$this->forge->addForeignKey(
			'sms_id_emisor',
			'usuarios',
			'usu_id_usuario',
			'SET NULL',
			'CASCADE',
			'fk_sms_emisor'
		);

		$this->forge->addForeignKey(
			'sms_id_receptor',
			'usuarios',
			'usu_id_usuario',
			'SET NULL',
			'CASCADE',
			'fk_sms_receptor'
		);

		$this->forge->createTable('mensajes_chat');
	}

	public function down()
	{
		$this->forge->dropTable('mensajes_chat', true);
	}
}
