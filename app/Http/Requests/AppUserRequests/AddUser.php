<?php

namespace App\Http\Requests\AppUserRequests;

use App\Rules\StrongPassword;
use Illuminate\Foundation\Http\FormRequest;

class AddUser extends FormRequest {
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
        return [
            'name' => 'required|max:60',
            'username' => 'max:60',
            'profile_photo_url' => 'required',
            // 'security_code' => 'required|max:6',
            'dob' => 'required',
            'phone_number' => 'required|unique:app_users',
        ];
    }
    public function messages()
    {
        return [
            'phone_number.required' => 'The phone number field is required.',
        ];
    }
}
