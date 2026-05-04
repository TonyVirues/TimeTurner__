<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class HomeController extends BaseController
{
  /**
   * Comprueba que el usuario haya iniciado sesión
   * @return mixed
   */
  private function exigirLogin()
  {
    if (!session()->get('isLoggedIn')) {
      return redirect()->to('/login');
    }

    return null;
  }

  /**
   * Muestra la vista principal del calendario dentro del layout del home
   * @return mixed
   */
  public function calendario()
  {
    $errorLogin = $this->exigirLogin();

    if ($errorLogin !== null) {
      return $errorLogin;
    }

    return view('home/home', [
      'vista_contenido' => 'home/calendario',
      'title' => 'Calendario | TimeTurner',
    ]);
  }

  /**
   * Muestra la vista de usuarios dentro del layout del home
   * @return mixed
   */
  public function usuarios()
  {
    $errorLogin = $this->exigirLogin();

    if ($errorLogin !== null) {
      return $errorLogin;
    }

    $usuarioModel = new UsuarioModel();
    $idEmpresa = (int) session()->get('usu_id_empresa');

    return view('home/home', [
      'vista_contenido' => 'home/usuarios',
      'usuarios' => $usuarioModel->getUsuariosPorEmpresa($idEmpresa),
      'title' => (session()->get('usu_rol') === 'administrador' ? 'Gestión de usuarios' : 'Compañeros') . ' | TimeTurner',
    ]);
  }

  /**
   * Muestra la vista de solicitudes dentro del layout del home
   * @return mixed
   */
  public function solicitudes()
  {
    $errorLogin = $this->exigirLogin();

    if ($errorLogin !== null) {
      return $errorLogin;
    }

    return view('home/home', [
      'vista_contenido' => 'home/solicitudes',
      'title' => 'Solicitudes | TimeTurner',
    ]);
  }
}
