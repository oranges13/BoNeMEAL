<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstallRequest;

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
        $langs = get_all_available_languages();
        $host = request()->getSchemeAndHttpHost();
        $timezones = list_all_available_timezones();

        return view('install.config', compact('host', 'langs', 'timezones'));
    }

    public function run(InstallRequest $request) {
        dd($request->validated());
    }

    public function finish() {

    }
}
