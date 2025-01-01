<?php

namespace App\Http\Requests\AppUserWeightsRequests;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWeights extends FormRequest {
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
            'weight' => 'required|regex:/^\d{1,6}$/',
        ];
        return $rules;
    }
}
