<?php

namespace App\Http\Requests\SchoolRequests;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSchool extends FormRequest {
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
            'short_name' => 'required|max:60',
            'website' => 'required',
            'street' => 'required|max:60',
            'street2' => 'required|max:60',
            'zipcode' => 'required|max:5',
            'state' => 'required',
            'city' => 'required|max:60',
        ];
        return $rules;
    }
}
