<?php

namespace App\Http\Requests;

use App\Models\PenjemputanTambahan;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePenjemputanTambahanRequest extends FormRequest
{
    public const ZONA_WAKTU = 'Asia/Jakarta';

    /** Pengajuan paling cepat untuk besok dan paling jauh 14 hari ke depan. */
    public const MAKS_HARI_KE_DEPAN = 14;

    public function authorize(): bool
    {
        return true;
    }

    public static function tanggalMinimal(): Carbon
    {
        return Carbon::today(self::ZONA_WAKTU)->addDay();
    }

    public static function tanggalMaksimal(): Carbon
    {
        return Carbon::today(self::ZONA_WAKTU)->addDays(self::MAKS_HARI_KE_DEPAN);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tanggal_diminta' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:' . self::tanggalMinimal()->toDateString(),
                'before_or_equal:' . self::tanggalMaksimal()->toDateString(),
            ],
            'alasan' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    /**
     * Satu warga hanya boleh punya satu pengajuan yang masih menunggu.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->user()->penjemputanTambahan()->menunggu()->exists()) {
                    $validator->errors()->add(
                        'tanggal_diminta',
                        'Anda masih punya pengajuan yang menunggu persetujuan. Tunggu diproses atau batalkan dulu.'
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_diminta.required' => 'Tanggal penjemputan wajib diisi.',
            'tanggal_diminta.date_format' => 'Format tanggal penjemputan tidak valid.',
            'tanggal_diminta.after_or_equal' => 'Penjemputan tambahan paling cepat untuk besok.',
            'tanggal_diminta.before_or_equal' => 'Penjemputan tambahan paling jauh ' . self::MAKS_HARI_KE_DEPAN . ' hari ke depan.',
            'alasan.required' => 'Alasan pengajuan wajib diisi.',
            'alasan.min' => 'Alasan pengajuan minimal 10 karakter.',
            'alasan.max' => 'Alasan pengajuan maksimal 500 karakter.',
        ];
    }
}
