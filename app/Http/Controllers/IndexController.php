<?php

namespace App\Http\Controllers;

use App\Models\AppUser;
use Illuminate\Http\Request;

class IndexController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        return view('welcome');
    }
    public function test() {

        $phoneNumbers = [
            '919662001633',
            '9974160430',
            '17788777777',
            '7788777777',
            '9122334455'
        ];

        $final_array = [];
        foreach ($phoneNumbers as $phone_number) {
            $phones = separateCountryCodeAndNumber($phone_number);
            $result = separateCountryCodeAndNumber($phone_number);
            $final_array[] = [
                'countryCode' => $result['countryCode'],
                'phoneNumber' => $result['phoneNumber'],
            ];
        }
        return json_encode($final_array);
    }

    public function userPublicProfile(Request $request) {
        $username = $request->username;
        $type = $request->type;
        $user = AppUser::where('username', $username)->first();
        return view('public_profile', compact('user', 'type', 'username'));
    }
}
