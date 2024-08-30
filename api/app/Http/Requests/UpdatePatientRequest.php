<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'national_code' => 'required|digits:10|unique:patients,national_code,' . $this->patient->id,
            'birthday' => 'required|date_format:Y/m/d',
            'gender' => 'required|in:male,female',
            'mobiles' => 'required|array|min:1',
            'mobiles.*' => 'required|numeric|digits:11|starts_with:09|unique:patient_mobiles,number,' . $this->patient->id . ',patient_id',
            'province' => 'required|exists:provinces,id',
            'city' => 'required|exists:cities,id',
            'lead_source' => 'required|exists:lead_sources,id',
            'status' => 'required|exists:patient_statuses,id'
        ];
    }
}
