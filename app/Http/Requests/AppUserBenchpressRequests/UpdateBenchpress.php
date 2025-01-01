<?php

namespace App\Http\Requests\AppUserBenchpressRequests;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBenchpress extends FormRequest {
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
            'bench_press' => 'required|regex:/^\d{1,6}$/',
        ];
        return $rules;
    }
}
