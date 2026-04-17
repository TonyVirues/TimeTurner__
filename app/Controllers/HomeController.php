<?php

namespace App\Controllers;

class HomeController extends BaseController
{
  public function calendario()
  {
    if (!session()->get('isLoggedIn')) {
      return redirect()->to('/login');
    }

    return view('home/calendario');
  }
}
