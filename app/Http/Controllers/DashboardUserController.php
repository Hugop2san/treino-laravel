<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardUserController extends Controller
{
    public function __invoke(): View
    {
        return view('logado');
    }
}
