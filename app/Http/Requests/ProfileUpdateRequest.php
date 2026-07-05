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
            'nama' => ['required', 'string', 'max:255', 'regex:/^[\pL\s.,\'’()\/&-]+$/u'],
            'nomor_induk' => ['required', 'string', 'max:50', 'regex:/^[\pL\pN\s.,\'’()\/&-]+$/u', Rule::unique(User::class)->ignore($this->user()->id)],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.regex' => 'Pastikan penulisan nama sama persis dengan ejaan di SK/Spreadsheet (titik dan koma diizinkan).',
            'nomor_induk.regex' => 'Nomor Induk hanya boleh berisi huruf, angka, spasi, dan tanda baca umum.',
            'nomor_induk.max' => 'Nomor Induk maksimal 50 karakter.',
            'nomor_induk.unique' => 'Nomor Induk ini sudah dipakai oleh pengguna lain.',
        ];
    }
}
