<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class Home extends BaseController
{
    public function calendario(): string
    {
        return view('home/home', [
            'vista_contenido' => 'home/calendario'
        ]);
    }

    public function usuarios(): string
    {
        $usuarioModel = new UsuarioModel();

        return view('home/home', [
            'vista_contenido' => 'home/usuarios',
            'usuarios' => $usuarioModel->getUsuarios()
        ]);
    }
}