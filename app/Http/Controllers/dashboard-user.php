<?php

namespace App\Http\Controllers;

class Dashboarduser extends Controller
{
    public function index()
    {
        
        $user =  User::orderByDesc('id')->get();

        return view(arquivoda.view, compact('user')) ;
    }


}