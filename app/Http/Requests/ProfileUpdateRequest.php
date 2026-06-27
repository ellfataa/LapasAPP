<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'nomor_induk' => ['required', 'string', 'max:18', 'regex:/^[0-9]+$/', Rule::unique(User::class)->ignore($this->user()->id)],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.regex' => 'Nama Lengkap hanya boleh berisi huruf dan spasi.',
            'nomor_induk.regex' => 'Nomor Induk hanya boleh berisi angka.',
            'nomor_induk.max' => 'Nomor Induk maksimal 18 digit.',
            'nomor_induk.unique' => 'Nomor Induk ini sudah dipakai oleh pengguna lain.',
        ];
    }
}
