<?php

namespace App\Models;

use CodeIgniter\Model;

class EmpresaModel extends Model
{
  protected $table = 'empresas';
  protected $primaryKey = 'emp_id_empresa';

  protected $returnType = 'array';
  protected $useAutoIncrement = true;
  protected $protectFields = true;

  protected $allowedFields = [
    'emp_nombre',
    'emp_cif',
    'emp_activa',
  ];
}
