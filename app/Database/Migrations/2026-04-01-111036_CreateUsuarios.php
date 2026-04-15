<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateUsuarios extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'usu_id_usuario' => [
        'type' => 'INT',
        'constraint' => 10,
        'unsigned' => true,
        'auto_increment' => true,
      ],
      'usu_id_empresa' => [
        'type' => 'INT',
        'constraint' => 10,
        'unsigned' => true,
        'null' => true,
      ],
      'usu_tipo_cuenta' => [
        'type' => 'ENUM',
        'constraint' => ['empresa', 'personal'],
        'null' => false,
      ],
      'usu_nombre' => [
        'type' => 'VARCHAR',
        'constraint' => 100,
      ],
      'usu_apellidos' => [
        'type' => 'VARCHAR',
        'constraint' => 150,
      ],
      'usu_email' => [
        'type' => 'VARCHAR',
        'constraint' => 150,
      ],
      'usu_password' => [
        'type' => 'VARCHAR',
        'constraint' => 255,
      ],
      'usu_rol' => [
        'type' => 'ENUM',
        'constraint' => ['administrador', 'empleado'],
        'default' => 'empleado',
      ],
      'usu_activo' => [
        'type' => 'TINYINT',
        'constraint' => 1,
        'default' => 1,
      ],
      'usu_creado' => [
        'type' => 'TIMESTAMP',
        'null' => false,
        'default' => new RawSql('CURRENT_TIMESTAMP'),
      ],
      'usu_actualizado' => [
        'type' => 'TIMESTAMP',
        'null' => false,
        'default' => new RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
      ],
    ]);

    $this->forge->addKey('usu_id_usuario', true);
    $this->forge->addKey('usu_id_empresa');
    $this->forge->addUniqueKey('usu_email', 'uk_usuarios_email');

    $this->forge->addForeignKey(
      'usu_id_empresa',
      'empresas',
      'emp_id_empresa',
      'CASCADE',
      'CASCADE',
      'fk_usuarios_empresa'
    );

    $this->forge->createTable('usuarios');
  }

  public function down()
  {
    $this->forge->dropTable('usuarios', true);
  }
}
