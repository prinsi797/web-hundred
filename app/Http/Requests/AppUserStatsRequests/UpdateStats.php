<?php

namespace App\Http\Requests\AppUserStatsRequests;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStats extends FormRequest {
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
            'power_clean' => 'required_without:deadlift|nullable|regex:/^\d{1,6}$/',
            'squat' => 'required|regex:/^\d{1,6}$/',
            'bench_press' => 'required|regex:/^\d{1,6}$/',
            'deadlift' => 'required_without:power_clean|nullable|regex:/^\d{1,6}$/',
        ];
        return $rules;
    }
}
