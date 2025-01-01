<?php

namespace App\Http\Requests\AppUserHeightsRequests;

use Illuminate\Foundation\Http\FormRequest;

class AddHeights extends FormRequest {
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
            'inch' => 'required|regex:/^\d{1,6}(\.\d+)?$/',
            'fit' => 'required|regex:/^\d{1,6}(\.\d+)?$/',
        ];
    }
}
