<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolIdentityUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_name' => ['required', 'string', 'max:255'],
            'school_npsn' => ['nullable', 'string', 'max:32', 'regex:/^\d{8}$/'],
            'school_email' => ['required', 'string', 'email', 'max:255'],
            'school_phone' => ['nullable', 'string', 'max:32'],
            'school_city' => ['nullable', 'string', 'max:120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'school_name' => 'nama sekolah',
            'school_npsn' => 'NPSN',
            'school_email' => 'email sekolah',
            'school_phone' => 'telepon sekolah',
            'school_city' => 'kota',
        ];
    }
}
