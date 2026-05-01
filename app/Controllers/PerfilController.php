<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class PerfilController extends BaseController
{
    protected UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Muestra la vista del perfil del usuario logueado
     */
    public function perfil()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $idUsuario = (int) session()->get('usu_id_usuario');
        $idEmpresa = (int) session()->get('usu_id_empresa');
        $usuario = $this->usuarioModel->getUsuarioPorId($idUsuario);

        $empresaModel = new \App\Models\EmpresaModel();
        $empresa = $empresaModel->find($idEmpresa);

        return view('home/home', [
            'vista_contenido' => 'home/perfil',
            'usuario' => $usuario,
            'empresa' => $empresa,
        ]);
    }

    /**
     * Actualiza los datos del perfil del usuario logueado
     */
    public function actualizar(): \CodeIgniter\HTTP\ResponseInterface
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Debes iniciar sesión.',
            ]);
        }

        $idUsuario = (int) session()->get('usu_id_usuario');
        $usuario = $this->usuarioModel->getUsuarioPorId($idUsuario);
        $datos = $this->request->getPost();
        $datosActualizar = [];

        // Validación de contraseñas
        if (!empty($datos['usu_password']) || !empty($datos['usu_password_confirm'])) {
            if ($datos['usu_password'] !== ($datos['usu_password_confirm'] ?? '')) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Las contraseñas no coinciden.',
                ]);
            }

            if (strlen($datos['usu_password']) < 8) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'La contraseña debe tener al menos 8 caracteres.',
                ]);
            }
        }

        if (!empty($datos['usu_nombre'])) {
            $datosActualizar['usu_nombre'] = $datos['usu_nombre'];
        }

        if (!empty($datos['usu_apellidos'])) {
            $datosActualizar['usu_apellidos'] = $datos['usu_apellidos'];
        }

        if (!empty($datos['usu_email'])) {
            if (
                $datos['usu_email'] !== $usuario['usu_email'] &&
                $this->usuarioModel->existeEmail($datos['usu_email'])
            ) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Ya existe otro usuario con ese email.',
                ]);
            }
            $datosActualizar['usu_email'] = $datos['usu_email'];
        }

        if (!empty($datos['usu_password'])) {
            $datosActualizar['usu_password'] = $datos['usu_password'];
        }

        if (empty($datosActualizar)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'No se han enviado datos para actualizar.',
            ]);
        }

        $resultado = $this->usuarioModel->update($idUsuario, $datosActualizar);

        if (!$resultado) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'No se pudo actualizar el perfil.',
            ]);
        }

        // Actualizamos los datos de sesión si cambió nombre o email
        if (isset($datosActualizar['usu_nombre'])) {
            session()->set('usu_nombre', $datosActualizar['usu_nombre']);
        }
        if (isset($datosActualizar['usu_email'])) {
            session()->set('usu_email', $datosActualizar['usu_email']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Perfil actualizado correctamente.',
        ]);
    }
}