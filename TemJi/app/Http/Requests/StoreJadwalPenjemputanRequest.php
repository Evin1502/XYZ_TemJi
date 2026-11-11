<?php

namespace App\Http\Requests;

use App\Models\JadwalPenjemputan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreJadwalPenjemputanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Kuota hanya berlaku untuk jadwal daur ulang.
     */
    protected function prepareForValidation(): void
    {
        if ($this->input('jenis') === JadwalPenjemputan::JENIS_SAMPAH_BIASA) {
            $this->merge(['kuota' => null]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'jenis' => ['required', Rule::in(array_keys(JadwalPenjemputan::JENIS))],
            'wilayah_id' => ['required', 'integer', Rule::exists('wilayah', 'id')->where('is_aktif', 1)],
            'hari' => ['required', 'integer', Rule::in(array_keys(JadwalPenjemputan::HARI))],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'kuota' => ['nullable', 'required_if:jenis,' . JadwalPenjemputan::JENIS_DAUR_ULANG, 'integer', 'min:1'],
            'petugas_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', 'petugas')],
        ];
    }

    /**
     * Tolak jadwal bila petugas sudah bertugas di hari dan jam yang tumpang-tindih.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                if (JadwalPenjemputan::adaBentrok(
                    $this->integer('petugas_id'),
                    $this->integer('hari'),
                    $this->input('jam_mulai'),
                    $this->input('jam_selesai'),
                )) {
                    $validator->errors()->add('petugas_id', 'Petugas sudah punya jadwal lain di hari dan jam tersebut.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'jenis.required' => 'Jenis jadwal wajib dipilih.',
            'jenis.in' => 'Jenis jadwal tidak valid.',
            'wilayah_id.required' => 'Wilayah wajib dipilih.',
            'wilayah_id.exists' => 'Wilayah tidak ditemukan atau sudah nonaktif.',
            'hari.required' => 'Hari wajib dipilih.',
            'hari.in' => 'Hari harus antara Senin sampai Sabtu.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_mulai.date_format' => 'Format jam mulai harus JJ:MM.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.date_format' => 'Format jam selesai harus JJ:MM.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'kuota.required_if' => 'Kuota wajib diisi untuk jadwal daur ulang.',
            'kuota.integer' => 'Kuota harus berupa angka bulat.',
            'kuota.min' => 'Kuota minimal 1 rumah.',
            'petugas_id.required' => 'Petugas wajib dipilih.',
            'petugas_id.exists' => 'Petugas yang dipilih tidak valid.',
        ];
    }
}
