<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
  public function index()
  {
    return view('auth/login');
  }

  // @mar para @tony Esto se está usando?
  public function pie()
  {
    return view('layouts/footer');
  }

  // Esto habrá que conectarlo con la DB tabla usuarios. Cambiar nombres para que coincidan con los campos de la tabla
  //     public function login()
  //     {
  //         $usuario = $this->request->getPost('usuario');
  //         $password = $this->request->getPost('password');

  //         $model = new UserModel();
  //         $user = $model->where('usuario', $usuario)->first();

  //         if ($user && password_verify($password, $user['password'])) {

  //             session()->set([
  //                 'id' => $user['id'],
  //                 'usuario' => $user['usuario'],
  //                 'logged_in' => true
  //             ]);

  //             return redirect()->to('/dashboard');
  //         }

  //         return redirect()->back()->with('error', 'Credenciales incorrectas');
  //     }
}
