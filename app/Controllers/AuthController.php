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
}
