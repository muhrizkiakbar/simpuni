<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index(string $file)
    {
        return response('found'.$file, 200);
    }
}
