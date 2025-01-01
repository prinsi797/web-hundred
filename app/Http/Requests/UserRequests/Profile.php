<?php

namespace App\Http\Requests\UserRequests;

use App\Rules\StrongPassword;
use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Http\FormRequest;

class Profile extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $is_admin = (auth()->user()->isAdmin);
        $rules = [
            'first_name' => 'required|max:60',
            'last_name' => 'required|max:60',
            'email' =>  "unique:users,email,{$this->id},id",
            'password' => ['nullable', new StrongPassword], // Add the StrongPassword rule
            'password_confirmation' => 'same:password',
        ];
        if (!$is_admin) {
            $rules['password'] = ['nullable', 'required_with:old_password', new StrongPassword];
            $rules['old_password'] = 'nullable|required_with:password|string';
        }
        return $rules;
    }
}
