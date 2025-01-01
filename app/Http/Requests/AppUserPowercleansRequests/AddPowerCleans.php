<?php

namespace App\Http\Requests\AppUserPowercleansRequests;

use Illuminate\Foundation\Http\FormRequest;

class AddPowerCleans extends FormRequest {
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
            'power_clean' => 'required|regex:/^\d{1,6}$/',
        ];
    }
}
