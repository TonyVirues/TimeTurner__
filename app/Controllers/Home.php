<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('main_content_calendario');
    }
}
