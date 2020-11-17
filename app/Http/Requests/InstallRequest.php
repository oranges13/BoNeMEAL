<?php

namespace App\Http\Requests;

use ErrorException;
use Illuminate\Foundation\Http\FormRequest;

class InstallRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'host' => 'required|url',
            'locale' => 'required|min:2|max:12',
            'timezone' => 'required|timezone',
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|confirmed|min:8',
            'database_type' => 'required|string',
            'db_host' => 'required_if:database_type,mysql',
            'db_port' => 'required_if:database_type,mysql|nullable|integer',
            'db_username' => 'required_if:database_type,mysql',
            'db_password' => 'required_if:database_type,mysql',
            'db_database' => 'required_if:database_type,mysql',
            'db_prefix' => 'nullable|string',
        ];
    }

    /**
     * After Validation Hook to ensure custom database is reachable
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->database_type === 'mysql') {
                $connect = mysqli_init();
                try{
                    $connect->real_connect($this->db_host, $this->db_username, $this->db_password, $this->db_database,
                        $this->db_port, null, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT);
                } catch (ErrorException $e) {
                    $validator->errors()->add('database_type', __('app.dbNotConnect') . " " . mysqli_connect_error());
                }
            }
        });

        return $validator;
    }
}
