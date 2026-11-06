<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistrasiWargaRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'nik' => ['required', 'numeric', 'digits:16', 'unique:warga,nik'],
            'no_kk' => ['required', 'numeric', 'digits:16'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'no_hp' => ['required', 'string', 'min:10', 'max:15', 'regex:/^[0-9+\-\s]+$/'],
            'wilayah_id' => [
                'required',
                'integer',
                Rule::exists('wilayah', 'id')->where('is_aktif', true),
            ],
            'alamat' => ['required', 'string', 'max:500'],
            'foto_identitas' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Pesan validasi berbahasa Indonesia yang jelas untuk warga.
     */
    public function messages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap sesuai KTP wajib diisi.',
            'nama_lengkap.max' => 'Nama lengkap maksimal 100 karakter.',
            'nik.required' => 'Nomor Induk Kependudukan (NIK) wajib diisi.',
            'nik.numeric' => 'NIK harus berupa angka tanpa spasi atau tanda baca.',
            'nik.digits' => 'NIK harus terdiri dari tepat 16 digit angka.',
            'nik.unique' => 'NIK ini sudah terdaftar pada sistem TemJi.',
            'no_kk.required' => 'Nomor Kartu Keluarga (No. KK) wajib diisi.',
            'no_kk.numeric' => 'Nomor KK harus berupa angka.',
            'no_kk.digits' => 'Nomor KK harus terdiri dari tepat 16 digit angka.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah digunakan oleh akun lain.',
            'no_hp.required' => 'Nomor HP / WhatsApp aktif wajib diisi.',
            'no_hp.min' => 'Nomor HP minimal 10 digit.',
            'no_hp.max' => 'Nomor HP maksimal 15 karakter.',
            'no_hp.regex' => 'Format nomor HP tidak valid.',
            'wilayah_id.required' => 'Wilayah domisili (Sukapura / Sukabirus) wajib dipilih.',
            'wilayah_id.exists' => 'Wilayah yang dipilih tidak valid atau sedang tidak aktif.',
            'alamat.required' => 'Alamat lengkap domisili wajib diisi.',
            'alamat.max' => 'Alamat lengkap maksimal 500 karakter.',
            'foto_identitas.required' => 'Foto/scan bukti identitas (KTP atau KK) wajib diunggah.',
            'foto_identitas.image' => 'Berkas bukti identitas harus berupa gambar.',
            'foto_identitas.mimes' => 'Format foto identitas harus berupa JPG, JPEG, atau PNG.',
            'foto_identitas.max' => 'Ukuran foto identitas maksimal 2 MB (2048 KB).',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ];
    }
}
