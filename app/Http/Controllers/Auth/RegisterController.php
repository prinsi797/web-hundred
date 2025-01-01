<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Rules\StrongPassword;
use App\Rules\Recaptcha;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make(
            $data,
            [
                'first_name' => ['required', 'string', 'max:80'],
                'last_name' => ['required', 'string', 'max:80'],
                'company' => ['required', 'string', 'max:100'],
                'street' => ['required', 'string', 'max:150'],
                'street2' => ['required', 'string', 'max:150'],
                'state' => ['required', 'string', 'max:150'],
                'phone_number' => ['required', 'string', 'max:150'],
                'city' => ['required', 'string', 'max:80'],
                'zipcode' => ['required', 'string', 'max:5'],
                'country_code' => ['required', 'string', 'max:150'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', new StrongPassword],
                'password_confirmation' => 'same:password',

                'g-recaptcha-response' =>  ['required', new Recaptcha()]
            ],
            [
                'g-recaptcha-response.required' => 'ReCaptcha verification failed.'
            ]
        );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */

    protected function create(array $data)
    {
        $data = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'company' => $data['company'],
            'street' => $data['street'],
            'street2' => $data['street2'],
            'city' => $data['city'],
            'state' => $data['state'],
            'zipcode' => $data['zipcode'],
            'country_code' => $data['country_code'],
            'phone_number' => $data['phone_number'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_non_profit' => isset($data['is_non_profit']) ? $data['is_non_profit'] : null,
        ];
        $tmpWhere = [
            'email' => $data['email'],
        ];
        $user = User::updateOrCreate($tmpWhere, $data);
        
        $user->syncRoles('User');

        // Try Creating a stripe customer.
        $stripCustomerObject = [
            'name' => $user->name,
            'email' => $user->email,
            'address' => [
                'city' => $user->city,
                'country' => 'US',
                'line1' => $user->street,
                'line2' => $user->street2,
                'postal_code' => $user->zipcode,
                'state' => $user->state,
            ]
        ];

        try {
            $stripCustomer = $user->createOrGetStripeCustomer($stripCustomerObject);
            Log::info("New Stripe customer has been created", ['stripCustomerObject' => $stripCustomer]);
        } catch (\Exception $exception) {
            Log::error("Error while creating a customer in stripe.", ['error' => $exception->getMessage()]);
        }
        return $user;
    }

    public function showRegistrationForm() {
        return view('theme.auth.register');
    }
}
