<?php

namespace App\Http\Requests;

use App\Models\PenjemputanTambahan;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SetujuiPenjemputanTambahanRequest extends FormRequest
{
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
            'petugas_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', 'petugas')],
            'tanggal_jemput' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:' . Carbon::today(StorePenjemputanTambahanRequest::ZONA_WAKTU)->toDateString(),
            ],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ];
    }

    /**
     * Tolak bila petugas sudah bertugas di jadwal rutin atau penjemputan tambahan lain pada waktu itu,
     * supaya permintaan tambahan tidak mengacaukan rute rutin.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $tanggal = Carbon::createFromFormat('Y-m-d', $this->input('tanggal_jemput'));

                // Jadwal petugas hanya Senin sampai Sabtu, sama seperti jadwal rutin
                if ($tanggal->isSunday()) {
                    $validator->errors()->add('tanggal_jemput', 'Penjemputan hanya dapat dijadwalkan Senin sampai Sabtu.');

                    return;
                }

                if (PenjemputanTambahan::petugasBentrok(
                    $this->integer('petugas_id'),
                    $tanggal,
                    $this->input('jam_mulai'),
                    $this->input('jam_selesai'),
                    (int) $this->route('id'),
                )) {
                    $validator->errors()->add('petugas_id', 'Petugas sudah punya jadwal lain pada tanggal dan jam tersebut.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'petugas_id.required' => 'Petugas wajib dipilih.',
            'petugas_id.exists' => 'Petugas yang dipilih tidak valid.',
            'tanggal_jemput.required' => 'Tanggal jemput wajib diisi.',
            'tanggal_jemput.date_format' => 'Format tanggal jemput tidak valid.',
            'tanggal_jemput.after_or_equal' => 'Tanggal jemput tidak boleh sebelum hari ini.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_mulai.date_format' => 'Format jam mulai harus JJ:MM.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.date_format' => 'Format jam selesai harus JJ:MM.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
        ];
    }
}
