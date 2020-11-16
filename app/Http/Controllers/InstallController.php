<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstallController extends Controller
{
    public function __construct()
    {
        $this->middleware('need_to_install');
    }

    public function index() {
        return view('install.index');
    }

    public function config() {

    }

    public function run() {

    }

    public function finish() {

    }
}
