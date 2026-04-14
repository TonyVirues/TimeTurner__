<?php

namespace App\Controllers;

class HomeController extends BaseController
{
  public function calendario(): string
  {
    return view('home/calendario');
  }
}
