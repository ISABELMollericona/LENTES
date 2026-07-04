<?php

namespace App\Http\Controllers\AsesorVirtual;

use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    public function index()
    {
        return view('asesor.index');
    }
}
