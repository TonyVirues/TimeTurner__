<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateEmpresas extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'emp_id_empresa' => [
        'type' => 'INT',
        'constraint' => 10,
        'unsigned' => true,
        'auto_increment' => true,
      ],
      'emp_nombre' => [
        'type' => 'VARCHAR',
        'constraint' => 150,
        'null' => false,
      ],
      'emp_cif' => [
        'type' => 'VARCHAR',
        'constraint' => 20,
        'null' => true,
      ],
      'emp_activa' => [
        'type' => 'TINYINT',
        'constraint' => 1,
        'default' => 1,
      ],
      'emp_creada' => [
        'type' => 'TIMESTAMP',
        'null' => false,
        'default' => new RawSql('CURRENT_TIMESTAMP'),
      ],
      'emp_actualizada' => [
        'type' => 'TIMESTAMP',
        'null' => false,
        'default' => new RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
      ],
    ]);

    $this->forge->addKey('emp_id_empresa', true);
    $this->forge->addUniqueKey('emp_cif', 'uk_empresas_cif');

    $this->forge->createTable('empresas');
  }

  public function down()
  {
    $this->forge->dropTable('empresas', true);
  }
}
