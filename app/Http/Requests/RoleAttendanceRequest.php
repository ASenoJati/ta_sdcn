<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Memastikan kombinasi role dan setting bersifat unik atau tervalidasi
            'role_id' => 'required|exists:roles,id',
            'attendance_time_settings_id' => 'required|exists:attendance_time_settings,id',
        ];
    }
}
