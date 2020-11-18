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

//        if ($request->database_type === 'mysql') {
//            $connect = mysqli_init();
//            try{
//                $connect->real_connect($request->db_host, $request->db_username, $request->db_password, $request->db_database,
//                    $request->db_port, null, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT);
//            } catch (ErrorException $e) {
//                dump($e->getMessage());
////                return redirect()->route('install.config')->withInput()->with('message', trans('app.dbNotConnect'))->with('messageDetails', mysqli_connect_error());
////                return redirect()->route('install.config')->withErrors($validator)->withInput();
//            }
//        }

        // Write .env file

        // Clear config cache

        // Migrate Database
    }

    public function finish() {

    }
}
