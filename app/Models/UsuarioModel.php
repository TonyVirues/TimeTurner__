<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
  protected $table = 'usuarios';
  protected $primaryKey = 'usu_id_usuario';

  protected $returnType = 'array';
  protected $useAutoIncrement = true;

  protected $protectFields = true;
  protected $beforeInsert = ['hashPassword'];
  protected $beforeUpdate = ['hashPassword'];

  protected $allowedFields = [
    'usu_id_empresa',
    'usu_tipo_cuenta',
    'usu_nombre',
    'usu_apellidos',
    'usu_email',
    'usu_password',
    'usu_rol',
    'usu_activo',
  ];

  protected bool $allowEmptyInserts = false;
  protected bool $updateOnlyChanged = true;

  protected array $casts = [
    'usu_id_usuario' => 'integer',
    'usu_id_empresa' => 'integer',
    'usu_activo' => 'integer',
  ];

  /**
   * Devuelve todos los usuarios ordenados por apellidos y nombre
   * @return array
   */
  public function getUsuarios(): array
  {
    return $this->orderBy('usu_apellidos', 'ASC')
      ->orderBy('usu_nombre', 'ASC')
      ->findAll();
  }

  /**
   * Devuelve un usuario por su id
   * @param int $idUsuario
   * @return array|null
   */
  public function getUsuarioPorId(int $idUsuario): ?array
  {
    return $this->find($idUsuario);
  }

  /**
   * Devuelve un usuario por su email (login)
   * @param string $email
   * @return array|null
   */
  public function getUsuarioPorEmail(string $email): ?array
  {
    return $this->where('usu_email', $email)->first();
  }

  /**
   * Devuelve todos los usuarios de una empresa
   * @param int $idEmpresa
   * @return array
   */
  public function getUsuariosPorEmpresa(int $idEmpresa): array
  {
    return $this->where('usu_id_empresa', $idEmpresa)
      ->orderBy('usu_apellidos', 'ASC')
      ->orderBy('usu_nombre', 'ASC')
      ->findAll();
  }

  /**
   * Devuelve los empleados de una empresa
   * @param int $idEmpresa
   * @return array
   */
  public function getEmpleadosPorEmpresa(int $idEmpresa): array
  {
    return $this->where('usu_id_empresa', $idEmpresa)
      ->where('usu_rol', 'empleado')
      ->orderBy('usu_apellidos', 'ASC')
      ->orderBy('usu_nombre', 'ASC')
      ->findAll();
  }

  /**
   * Devuelve los administradores de una empresa
   * @param int $idEmpresa
   * @return array
   */
  public function getAdministradoresPorEmpresa(int $idEmpresa): array
  {
    return $this->where('usu_id_empresa', $idEmpresa)
      ->where('usu_rol', 'administrador')
      ->orderBy('usu_apellidos', 'ASC')
      ->orderBy('usu_nombre', 'ASC')
      ->findAll();
  }

  /**
   * Comprueba si existe ya un usuario con ese email
   * @param string $email
   * @return bool
   */
  public function existeEmail(string $email): bool
  {
    return $this->where('usu_email', $email)->countAllResults() > 0;
  }

  /**
   * Hashea la contraseña antes de insertar o actualizar
   * @param array $data
   * @return array
   */
  protected function hashPassword(array $data): array
  {
    if (!isset($data['data']['usu_password'])) {
      return $data;
    }

    $data['data']['usu_password'] = password_hash(
      $data['data']['usu_password'],
      PASSWORD_DEFAULT
    );

    return $data;
  }
}