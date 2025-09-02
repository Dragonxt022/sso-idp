<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Support\Facades\Auth;

class ConfigController extends Controller
{
    public function index()
    {

        return view('config.index');
    }
}
