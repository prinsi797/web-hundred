<?php

namespace App\Http\Requests\UserRequests;

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
            'first_name' => 'required|max:60',
            'last_name' => 'required|max:60',
            'email' =>  "unique:users,email,{$this->id},id",
        ];
        return $rules;
    }
}
