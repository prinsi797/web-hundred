<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Rules\Recaptcha;
use App\Notifications\TwoFactorCode;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest')->except('logout');
    }
    public function showLoginForm() {
        return kview('auth.login');
    }
    // protected function validateLogin(Request $request) {
    //     $request->validate(
    //         [
    //             'g-recaptcha-response' =>  ['required', new Recaptcha()]
    //         ],
    //         [
    //             'g-recaptcha-response.required' => 'ReCaptcha verification failed.'
    //         ]
    //     );
    // }

    public function authenticated(Request $request, $user) {
        try {
            if ($user->two_factor_enable) {
                $user->generateTwoFactorCode();
                $user->notify(new TwoFactorCode());
            }
        } catch (\Exception $e) {
            auth()->logout();
            return redirect()->back()->withMessage("Something went wrong, Please try again after some time.");
        }
    }
}
