<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\EmpresaModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class UsuariosController extends BaseController
{
  protected UsuarioModel $usuarioModel;
  protected EmpresaModel $empresaModel;

  public function initController(
    RequestInterface $request,
    ResponseInterface $response,
    LoggerInterface $logger
  ) {
    parent::initController($request, $response, $logger);
    $this->usuarioModel = new UsuarioModel();
    $this->empresaModel = new EmpresaModel();
  }

  /**
   * Devuelve todos los usuarios
   * @return ResponseInterface
   */
  public function listado(): ResponseInterface
  {
    $usuarios = $this->usuarioModel->getUsuarios();

    foreach ($usuarios as &$usuario) {
      unset($usuario['usu_password']);
    }

    return $this->response->setJSON([
      'status' => 'success',
      'data' => $usuarios,
    ]);
  }

  /**
   * Devuelve un usuario concreto por su id
   * @param int $idUsuario
   * @return ResponseInterface
   */
  public function mostrar(int $idUsuario): ResponseInterface
  {
    $usuario = $this->usuarioModel->getUsuarioPorId($idUsuario);

    if (!$usuario) {
      return $this->responderNoEncontrado();
    }

    unset($usuario['usu_password']);

    return $this->response->setJSON([
      'status' => 'success',
      'data' => $usuario,
    ]);
  }

  /**
   * Crea un nuevo usuario
   * @return ResponseInterface
   */
  public function crear(): ResponseInterface
  {
    $datos = $this->request->getPost();

    if (!$this->validate($this->obtenerReglasUsuario(true))) {
      return $this->responderErrorValidacion();
    }

    $respuestaValidacionNegocio = $this->validarLogicaUsuario($datos);

    if ($respuestaValidacionNegocio !== null) {
      return $respuestaValidacionNegocio;
    }

    if ($this->usuarioModel->existeEmail($datos['usu_email'])) {
      return $this->response->setStatusCode(400)->setJSON([
        'status' => 'error',
        'message' => 'Ya existe un usuario con ese email.',
      ]);
    }

    $idEmpresa = $this->resolverEmpresaParaCreacion($datos);

    if ($idEmpresa instanceof ResponseInterface) {
      return $idEmpresa;
    }

    $datosInsertar = [
      'usu_id_empresa' => $idEmpresa,
      'usu_tipo_cuenta' => $datos['usu_tipo_cuenta'],
      'usu_nombre' => $datos['usu_nombre'],
      'usu_apellidos' => $datos['usu_apellidos'],
      'usu_email' => $datos['usu_email'],
      'usu_password' => $datos['usu_password'],
      'usu_rol' => $this->resolverRolParaCreacion($datos),
      'usu_activo' => isset($datos['usu_activo']) ? (int) $datos['usu_activo'] : 1,
    ];

    $resultado = $this->usuarioModel->insert($datosInsertar);

    if (!$resultado) {
      return $this->response->setStatusCode(500)->setJSON([
        'status' => 'error',
        'message' => 'No se pudo crear el usuario.',
      ]);
    }

    $usuarioCreado = $this->usuarioModel->getUsuarioPorId((int) $resultado);

    if ($usuarioCreado) {
      unset($usuarioCreado['usu_password']);
    }

    return $this->response->setStatusCode(201)->setJSON([
      'status' => 'success',
      'message' => 'Usuario creado correctamente.',
      'data' => $usuarioCreado,
    ]);
  }

  /**
   * Actualiza un usuario existente
   * @param int $idUsuario
   * @return ResponseInterface
   */
  public function actualizar(int $idUsuario): ResponseInterface
  {
    $usuario = $this->usuarioModel->getUsuarioPorId($idUsuario);

    if (!$usuario) {
      return $this->responderNoEncontrado();
    }

    $datos = $this->request->getPost();

    if (!$this->validate($this->obtenerReglasUsuario(false, $idUsuario))) {
      return $this->responderErrorValidacion();
    }

    $respuestaValidacionNegocio = $this->validarLogicaUsuario($datos, false);

    if ($respuestaValidacionNegocio !== null) {
      return $respuestaValidacionNegocio;
    }

    if (isset($datos['usu_email']) && $datos['usu_email'] !== $usuario['usu_email']) {
      if ($this->usuarioModel->existeEmail($datos['usu_email'])) {
        return $this->response->setStatusCode(400)->setJSON([
          'status' => 'error',
          'message' => 'Ya existe otro usuario con ese email.',
        ]);
      }
    }

    $datosActualizar = [];

    if (array_key_exists('usu_nombre', $datos)) {
      $datosActualizar['usu_nombre'] = $datos['usu_nombre'];
    }

    if (array_key_exists('usu_apellidos', $datos)) {
      $datosActualizar['usu_apellidos'] = $datos['usu_apellidos'];
    }

    if (array_key_exists('usu_email', $datos)) {
      $datosActualizar['usu_email'] = $datos['usu_email'];
    }

    if (!empty($datos['usu_password'])) {
      $datosActualizar['usu_password'] = $datos['usu_password'];
    }

    if (array_key_exists('usu_tipo_cuenta', $datos)) {
      $datosActualizar['usu_tipo_cuenta'] = $datos['usu_tipo_cuenta'];
    }

    if (array_key_exists('usu_activo', $datos)) {
      $datosActualizar['usu_activo'] = (int) $datos['usu_activo'];
    }

    if (array_key_exists('usu_tipo_cuenta', $datos)) {
      if ($datos['usu_tipo_cuenta'] === 'personal') {
        $datosActualizar['usu_id_empresa'] = null;
        $datosActualizar['usu_rol'] = 'administrador';
      }

      if ($datos['usu_tipo_cuenta'] === 'empresa') {
        if (isset($datos['usu_id_empresa']) && $datos['usu_id_empresa'] !== '') {
          $empresa = $this->empresaModel->find((int) $datos['usu_id_empresa']);

          if (!$empresa) {
            return $this->response->setStatusCode(400)->setJSON([
              'status' => 'error',
              'message' => 'La empresa indicada no existe.',
            ]);
          }

          $datosActualizar['usu_id_empresa'] = (int) $datos['usu_id_empresa'];
        }

        if (isset($datos['usu_rol'])) {
          $datosActualizar['usu_rol'] = $datos['usu_rol'];
        }
      }
    } else {
      if (isset($datos['usu_id_empresa']) && $datos['usu_id_empresa'] !== '') {
        $empresa = $this->empresaModel->find((int) $datos['usu_id_empresa']);

        if (!$empresa) {
          return $this->response->setStatusCode(400)->setJSON([
            'status' => 'error',
            'message' => 'La empresa indicada no existe.',
          ]);
        }

        $datosActualizar['usu_id_empresa'] = (int) $datos['usu_id_empresa'];
      }

      if (isset($datos['usu_rol'])) {
        $datosActualizar['usu_rol'] = $datos['usu_rol'];
      }
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
        'message' => 'No se pudo actualizar el usuario.',
      ]);
    }

    $usuarioActualizado = $this->usuarioModel->getUsuarioPorId($idUsuario);

    if ($usuarioActualizado) {
      unset($usuarioActualizado['usu_password']);
    }

    return $this->response->setJSON([
      'status' => 'success',
      'message' => 'Usuario actualizado correctamente.',
      'data' => $usuarioActualizado,
    ]);
  }

  /**
   * Elimina un usuario por su id
   * @param int $idUsuario
   * @return ResponseInterface
   */
  public function eliminar(int $idUsuario): ResponseInterface
  {
    $usuario = $this->usuarioModel->getUsuarioPorId($idUsuario);

    if (!$usuario) {
      return $this->responderNoEncontrado();
    }

    $resultado = $this->usuarioModel->delete($idUsuario);

    if (!$resultado) {
      return $this->response->setStatusCode(500)->setJSON([
        'status' => 'error',
        'message' => 'No se pudo eliminar el usuario.',
      ]);
    }

    return $this->response->setJSON([
      'status' => 'success',
      'message' => 'Usuario eliminado correctamente.',
    ]);
  }

  /**
   * Devuelve las reglas de validación del usuario
   * @param bool $passwordObligatoria
   * @param int|null $idUsuario
   * @return array
   */
  private function obtenerReglasUsuario(bool $passwordObligatoria = true, ?int $idUsuario = null): array
  {
    $reglasPassword = $passwordObligatoria
      ? 'required|min_length[8]'
      : 'permit_empty|min_length[8]';

    return [
      'usu_tipo_cuenta' => 'required|in_list[empresa,personal]',
      'usu_nombre' => 'required|min_length[2]|max_length[100]',
      'usu_apellidos' => 'required|min_length[2]|max_length[150]',
      'usu_email' => 'required|valid_email|max_length[150]',
      'usu_password' => $reglasPassword,
      'usu_rol' => 'permit_empty|in_list[administrador,empleado]',
      'usu_activo' => 'permit_empty|in_list[0,1]',
      'usu_id_empresa' => 'permit_empty|integer',
      'emp_nombre' => 'permit_empty|min_length[2]|max_length[150]',
      'emp_cif' => 'permit_empty|max_length[20]',
    ];
  }

  /**
   * Valida la lógica de negocio del usuario, 
   * comprueba que los datos respetan las reglas de la aplicación
   * @param array $datos
   * @param bool $esCreacion
   * @return ResponseInterface|null
   */
  private function validarLogicaUsuario(array $datos, bool $esCreacion = true): ?ResponseInterface
  {
    if (!isset($datos['usu_tipo_cuenta'])) {
      return $this->response->setStatusCode(400)->setJSON([
        'status' => 'error',
        'message' => 'El tipo de cuenta es obligatorio.',
      ]);
    }

    if ($datos['usu_tipo_cuenta'] === 'personal') {
      if (!empty($datos['usu_id_empresa'])) {
        return $this->response->setStatusCode(400)->setJSON([
          'status' => 'error',
          'message' => 'Una cuenta personal no puede estar asociada a una empresa.',
        ]);
      }

      if (isset($datos['usu_rol']) && $datos['usu_rol'] !== 'administrador') {
        return $this->response->setStatusCode(400)->setJSON([
          'status' => 'error',
          'message' => 'Una cuenta personal solo puede tener rol administrador.',
        ]);
      }
    }

    if ($datos['usu_tipo_cuenta'] === 'empresa') {
      $tieneEmpresaExistente = !empty($datos['usu_id_empresa']);
      $tieneDatosNuevaEmpresa = !empty($datos['emp_nombre']);

      if (!$tieneEmpresaExistente && !$tieneDatosNuevaEmpresa && $esCreacion) {
        return $this->response->setStatusCode(400)->setJSON([
          'status' => 'error',
          'message' => 'Una cuenta de empresa debe indicar una empresa existente o crear una nueva.',
        ]);
      }

      if (isset($datos['usu_rol']) && !in_array($datos['usu_rol'], ['administrador', 'empleado'], true)) {
        return $this->response->setStatusCode(400)->setJSON([
          'status' => 'error',
          'message' => 'El rol indicado no es válido para una cuenta de empresa.',
        ]);
      }
    }

    return null;
  }

  /**
   * Determina qué empresa se asocia al crear un usuario
   * @param array $datos
   * @return int|ResponseInterface|null
   */
  private function resolverEmpresaParaCreacion(array $datos): int|ResponseInterface|null
  {
    if ($datos['usu_tipo_cuenta'] === 'personal') {
      return null;
    }

    if (!empty($datos['usu_id_empresa'])) {
      $empresa = $this->empresaModel->find((int) $datos['usu_id_empresa']);

      if (!$empresa) {
        return $this->response->setStatusCode(400)->setJSON([
          'status' => 'error',
          'message' => 'La empresa indicada no existe.',
        ]);
      }

      return (int) $datos['usu_id_empresa'];
    }

    if (!empty($datos['emp_nombre'])) {
      $datosEmpresa = [
        'emp_nombre' => $datos['emp_nombre'],
        'emp_cif' => !empty($datos['emp_cif']) ? $datos['emp_cif'] : null,
      ];

      $idEmpresa = $this->empresaModel->insert($datosEmpresa);

      if (!$idEmpresa) {
        return $this->response->setStatusCode(500)->setJSON([
          'status' => 'error',
          'message' => 'No se pudo crear la empresa.',
        ]);
      }

      return (int) $idEmpresa;
    }

    return null;
  }

  /**
   * Determina que rol debe tener el usuario al crearse
   * @param array $datos
   * @return string
   */
  private function resolverRolParaCreacion(array $datos): string
  {
    if ($datos['usu_tipo_cuenta'] === 'personal') {
      return 'administrador';
    }

    if (!empty($datos['usu_rol'])) {
      return $datos['usu_rol'];
    }

    return 'administrador';
  }

  /**
   * Devuelve respuesta de error de validación
   * @return ResponseInterface
   */
  private function responderErrorValidacion(): ResponseInterface
  {
    return $this->response->setStatusCode(400)->setJSON([
      'status' => 'error',
      'errors' => $this->validator->getErrors(),
    ]);
  }

  /**
   * Devuelve respuesta de no encontrado
   * @return ResponseInterface
   */
  private function responderNoEncontrado(): ResponseInterface
  {
    return $this->response->setStatusCode(404)->setJSON([
      'status' => 'error',
      'message' => 'Usuario no encontrado.',
    ]);
  }
}
