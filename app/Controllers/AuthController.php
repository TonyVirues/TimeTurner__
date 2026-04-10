<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
  public function login()
  {
    return view('auth/login');
  }

  public function registro()
  {
    return view('auth/registro');
  }

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
