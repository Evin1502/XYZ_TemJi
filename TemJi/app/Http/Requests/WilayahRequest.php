<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WilayahRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ganti dengan cek admin setelah autentikasi jadi
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_aktif' => $this->boolean('is_aktif')]);
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100',
                Rule::unique('wilayahs', 'nama')->ignore($this->route('wilayah'))],
            'jumlah_kk' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_aktif' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return ['nama' => 'nama wilayah', 'jumlah_kk' => 'jumlah KK'];
    }
}