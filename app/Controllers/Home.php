<?php

namespace App\Controllers;

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
        return view('home/home', [
            'vista_contenido' => 'home/usuarios'
        ]);
    }
}