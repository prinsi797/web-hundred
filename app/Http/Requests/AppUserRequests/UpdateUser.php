<?php

namespace App\Http\Requests\AppUserRequests;

use App\Rules\StrongPassword;
use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUser extends FormRequest {
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
        $rules = [
            'name' => 'required|max:60',
            'username' => 'max:60',
            'dob' => 'required',
            'phone_number' => 'required|unique:app_users',
        ];
        return $rules;
    }
}
