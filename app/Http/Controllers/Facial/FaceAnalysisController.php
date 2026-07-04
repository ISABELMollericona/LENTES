<?php

namespace App\Http\Controllers\Facial;

use App\Http\Controllers\Controller;

class FaceAnalysisController extends Controller
{
    public function index()
    {
        return view('facial.index');
    }
}
