<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $user = auth()->user();

        return view('dashboard/index', [
            'user' => $user,
        ]);
    }
}
