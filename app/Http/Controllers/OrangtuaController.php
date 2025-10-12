<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrangtuaController extends Controller
{
    public function dashboard()
    {
        return view('orangtua.dashboard');
    }
}
