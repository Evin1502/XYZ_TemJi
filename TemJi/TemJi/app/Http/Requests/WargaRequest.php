<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WargaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ganti dengan cek admin setelah autentikasi jadi
    }

    public function rules(): array
    {
        return [
            'no_kk' => ['required', 'digits:16',
                Rule::unique('warga', 'no_kk')->ignore($this->route('warga'))],
            'nama_kepala_keluarga' => ['required', 'string', 'max:100'],
            'alamat' => ['nullable', 'string', 'max:500'],
            // wajib pilih wilayah aktif agar statistik tidak bolong
            'wilayah_id' => ['required', Rule::exists('wilayah', 'id')->where('is_aktif', true)],
        ];
    }

    public function attributes(): array
    {
        return [
            'no_kk' => 'nomor KK',
            'nama_kepala_keluarga' => 'nama kepala keluarga',
            'wilayah_id' => 'wilayah',
        ];
    }
}
