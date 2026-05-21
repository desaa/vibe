<?php

namespace App\Controllers;

use CodeIgniter\Shield\Controllers\LoginController as ShieldLogin;

class LoginController extends ShieldLogin
{
    // Login View menggunakan template Velzone
    protected $viewPrefix = 'Auth\\';
}
