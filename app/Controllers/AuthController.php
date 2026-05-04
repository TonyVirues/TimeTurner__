<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\EmpresaModel;

class AuthController extends BaseController
{
  protected UsuarioModel $usuarioModel;
  protected EmpresaModel $empresaModel;

  public function __construct()
  {
    $this->usuarioModel = new UsuarioModel();
    $this->empresaModel = new EmpresaModel();
  }

  /**
   * Muestra la vista del login
   * @return string|\CodeIgniter\HTTP\RedirectResponse
   */
  public function login()
  {
    if (session()->get('isLoggedIn')) {
      return redirect()->to('/calendario');
    }

    return view('auth/login');
  }

  /**
   * Muestra la vista del registro
   * @return string|\CodeIgniter\HTTP\RedirectResponse
   */
  public function registro()
  {
    if (session()->get('isLoggedIn')) {
      return redirect()->to('/calendario');
    }

    return view('auth/registro');
  }

  /**
   * Comprueba las credenciales y crea la sesión del usuario
   * @return \CodeIgniter\HTTP\RedirectResponse
   */
  public function autenticar()
  {
    $email = mb_strtolower(trim((string) $this->request->getPost('email')));
    $password = (string) $this->request->getPost('password');

    if ($email === '' || $password === '') {
      return redirect()->back()
        ->withInput()
        ->with('error', 'Debes rellenar email y contraseña.');
    }

    $usuario = $this->usuarioModel->getUsuarioPorEmail($email);

    if (!$usuario) {
      return redirect()->back()
        ->withInput()
        ->with('error', 'Credenciales incorrectas.');
    }

    if ((int) $usuario['usu_activo'] !== 1) {
      return redirect()->back()
        ->withInput()
        ->with('error', 'Tu usuario está inactivo.');
    }

    if (!password_verify($password, $usuario['usu_password'])) {
      return redirect()->back()
        ->withInput()
        ->with('error', 'Credenciales incorrectas.');
    }

    session()->set([
      'usu_id_usuario' => $usuario['usu_id_usuario'],
      'usu_id_empresa' => $usuario['usu_id_empresa'],
      'usu_nombre' => $usuario['usu_nombre'],
      'usu_apellidos' => $usuario['usu_apellidos'],
      'usu_email' => $usuario['usu_email'],
      'usu_rol' => $usuario['usu_rol'],
      'isLoggedIn' => true,
    ]);

    return redirect()->to('/calendario');
  }

  /**
   * Registra una empresa y su primer usuario administrador
   * @return \CodeIgniter\HTTP\RedirectResponse
   */
  public function registrar()
  {
    $nombre = trim((string) $this->request->getPost('usu_nombre'));
    $apellidos = trim((string) $this->request->getPost('usu_apellidos'));
    $email = mb_strtolower(trim((string) $this->request->getPost('usu_email')));
    $password = (string) $this->request->getPost('usu_password');
    $nombreEmpresa = trim((string) $this->request->getPost('emp_nombre'));
    $cif = trim((string) $this->request->getPost('emp_cif'));
    $cpassword = (string) $this->request->getPost('cpassword');

    /**Campos obligatorios */
    if (
      $nombre === '' ||
      $apellidos === '' ||
      $email === '' ||
      $password === '' ||
      $cpassword === '' ||
      $nombreEmpresa === '' ||
      $cif === ''
    ) {
      return redirect()->back()
        ->withInput()
        ->with('error', 'Debes rellenar todos los campos obligatorios.')
        ->with('errorCampo', 'obligatorios');
    }

    /**Contraseñas no coinciden */
    if ($password !== $cpassword) {
      return redirect()->back()
        ->withInput()
        ->with('error', 'Las contraseñas no coinciden.')
        ->with('errorCampo', 'password');
    }
    /**Email inválido */
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return redirect()->back()
        ->withInput()
        ->with('error', 'El email no es válido.')
        ->with('errorCampo', 'email');
    }
    /**Contraseña corta */
    if (strlen($password) < 8) {
      return redirect()->back()
        ->withInput()
        ->with('error', 'La contraseña debe tener al menos 8 caracteres.')
        ->with('errorCampo', 'password');
    }
    /**Email duplicado */
    if ($this->usuarioModel->existeEmail($email)) {
      return redirect()->back()
        ->withInput()
        ->with('error', 'Ya existe un usuario con ese email.')
        ->with('errorCampo', 'email');
    }
    /**Nombre y apellido no pude contener números */
    if (preg_match('/\d/', $nombre) || preg_match('/\d/', $apellidos)) {
      return redirect()->back()
        ->withInput()
        ->with('error', 'El nombre y los apellidos no pueden contener números.')
        ->with('errorCampo', 'nombre');
    }
    /**Formato CIF inválido */
    $cif = mb_strtoupper($cif);

    if (!preg_match('/^[ABCDEFGHJKLMNPQRSUVW]\d{7}[0-9A-J]$/', $cif)) {
      return redirect()->back()
        ->withInput()
        ->with('error', 'El CIF no tiene un formato válido. Ejemplo: B12345678 o A1234567B')
        ->with('errorCampo', 'cif');
    }

    /**El CIF ya existe */
    if ($this->empresaModel->existeCif($cif)) {
      return redirect()->back()
        ->withInput()
        ->with('error', 'Ya existe una empresa registrada con ese CIF.')
        ->with('errorCampo', 'cif');
    }

    /**Array para validad las credenciales de la empresa*/
    $idEmpresa = $this->empresaModel->insert([
      'emp_nombre' => $nombreEmpresa,
      'emp_cif' => $cif,
      'emp_activa' => 1,
    ]);

    if (!$idEmpresa) {
      return redirect()->back()
        ->withInput()
        ->with('error', 'No se pudo crear la empresa.');
    }

    $idUsuario = $this->usuarioModel->insert([
      'usu_id_empresa' => (int) $idEmpresa,
      'usu_tipo_cuenta' => 'empresa',
      'usu_nombre' => $nombre,
      'usu_apellidos' => $apellidos,
      'usu_email' => $email,
      'usu_password' => $password,
      'usu_rol' => 'administrador',
      'usu_activo' => 1,
    ]);

    if (!$idUsuario) {
      return redirect()->back()
        ->withInput()
        ->with('error', 'No se pudo crear el usuario administrador.');
    }

    $usuario = $this->usuarioModel->getUsuarioPorId((int) $idUsuario);

    if (!$usuario) {
      return redirect()->to('/login')
        ->with('error', 'El registro se completó parcialmente, pero no se pudo iniciar sesión.');
    }

    session()->set([
      'usu_id_usuario' => $usuario['usu_id_usuario'],
      'usu_id_empresa' => $usuario['usu_id_empresa'],
      'usu_nombre' => $usuario['usu_nombre'],
      'usu_apellidos' => $usuario['usu_apellidos'],
      'usu_email' => $usuario['usu_email'],
      'usu_rol' => $usuario['usu_rol'],
      'isLoggedIn' => true,
    ]);

    return redirect()->to('/calendario');
  }

  /**
   * Cierra la sesión del usuario autenticado
   * @return \CodeIgniter\HTTP\RedirectResponse
   */
  public function logout()
  {
    session()->destroy();

    return redirect()->to('/login');
  }
}
