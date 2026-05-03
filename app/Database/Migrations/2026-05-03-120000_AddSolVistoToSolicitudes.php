<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSolVistoToSolicitudes extends Migration
{
    public function up()
    {
        $this->forge->addColumn('solicitudes_cambio_turno', [
            'sol_visto' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
                'after'      => 'sol_comentario_resolucion',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('solicitudes_cambio_turno', 'sol_visto');
    }
}