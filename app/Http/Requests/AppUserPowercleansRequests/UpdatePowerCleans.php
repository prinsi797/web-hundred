<?php

namespace App\Http\Requests\AppUserPowercleansRequests;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePowerCleans extends FormRequest {
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
            'power_clean' => 'required|regex:/^\d{1,6}$/',
        ];
        return $rules;
    }
}
