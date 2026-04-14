<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function calendario(): string
    {
        return view('home/calendario');
    }
}
