<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Password Reset Controller
	|--------------------------------------------------------------------------
	|
	| This controller is responsible for handling password reset requests
	| and uses a simple trait to include this behavior. You're free to
	| explore this trait and override any methods you wish to tweak.
	|
	*/

	use ResetsPasswords;

	/**
	 * Create a new password controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\PasswordBroker  $passwords
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
	}

	public function getEmail()
	{
		return $this->showLinkRequestForm();
	}

	public function postEmail(Request $request)
	{
		$request->validate(['email' => 'required|email']);

		$response = Password::broker()->sendResetLink($request->only('email'));

		return $response === Password::RESET_LINK_SENT
			? back()->with('status', __($response))
			: back()->withInput($request->only('email'))
				->withErrors(['email' => __($response)]);
	}

	public function getReset(Request $request, $token = null)
	{
		return $this->showResetForm($request, $token);
	}

	public function postReset(Request $request)
	{
		return $this->reset($request);
	}

	public function showLinkRequestForm()
	{
		return view('auth.password');
	}

	public function showResetForm(Request $request, $token = null)
	{
		return view('auth.reset')->with([
			'token' => $token,
			'email' => $request->email,
		]);
	}

	protected function rules()
	{
		return [
			'token' => 'required',
			'email' => 'required|email',
			'password' => 'required|confirmed|min:6',
		];
	}

}
