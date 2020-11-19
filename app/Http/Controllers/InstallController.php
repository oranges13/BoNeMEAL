<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstallRequest;
use App\User;
use ErrorException;
use Exception;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class InstallController extends Controller
{
    public function __construct()
    {
        $this->middleware('need_to_install');
    }

    public function index()
    {
        return view('install.index');
    }

    public function config()
    {
        $langs = get_all_available_languages();
        $host = request()->getSchemeAndHttpHost();
        $timezones = list_all_available_timezones();

        return view('install.config', compact('host', 'langs', 'timezones'));
    }

    public function run(InstallRequest $request)
    {
        if ($request->database_type === 'mysql') {
            $connect = mysqli_init();
            try {
                $connect->real_connect($request->db_host, $request->db_username, $request->db_password,
                    $request->db_database,
                    $request->db_port, null, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT);
            } catch (ErrorException $e) {
                return redirect()->route('install.config')->withInput()->with('error',
                    __('app.dbNotConnect') . mysqli_connect_error());
            }
        }

        $key = base64_encode(Str::random(32));

        // Write .env file
        $env = <<<ENV
            APP_NAME=BoNeMEAL
            APP_ENV=production
            APP_KEY=base64:{$key}
            APP_DEBUG=false
            APP_URL={$request->host}
            ENV;

        if ($request->database_type === 'sqlite') {
            $env .= <<<ENV

                DB_CONNECTION=sqlite
                ENV;
        }

        if ($request->database_Type === 'mysql') {
            $env .= <<<ENV

                DB_CONNECTION=mysql
                DB_HOST={$request->db_host}
                DB_PORT={$request->db_port}
                DB_DATABASE={$request->db_database}
                DB_USERNAME={$request->db_username}
                DB_PASSWORD={$request->db_password}
                DB_PREFIX={$request->db_prefix}
                ENV;
        }

        try {
            file_put_contents(base_path('.env'), $env);

            if (!defined('STDIN')) {
                define('STDIN', fopen("php://stdin", "r"));
            }

            // Clear config cache
            Artisan::call('config:clear');
            // Migrate Database
            Artisan::call('migrate:fresh', ['--force' => true]);

            // Create admin user
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);
        } catch (ErrorException | Exception $e) {
            return redirect()->route('install.config')->withInput()->with('error',
                'Could not install application! ' . $e->getMessage())->send();
        }

        file_put_contents(base_path('installed.lock'), time());
        return view('install.finish');
    }

}
