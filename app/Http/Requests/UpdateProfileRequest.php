<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'first_name'     => 'sometimes|string|max:255',
            'last_name'      => 'sometimes|string|max:255',
            'national_code'  => 'sometimes|string|max:10',
            'license_number' => 'sometimes|string|max:255',
            'description'    => 'nullable|string',
            'province_id'    => 'nullable|exists:zone,id,level,1', // فقط استان‌ها (level=1)
            'city_id'        => 'nullable|exists:zone,id,level,2', // فقط شهرها (level=2)
        ];
    }
}
