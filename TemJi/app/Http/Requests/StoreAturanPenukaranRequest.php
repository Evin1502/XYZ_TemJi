<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAturanPenukaranRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nilai_rupiah_per_poin' => ['required', 'integer', 'min:1'],
            'batas_maksimal_potongan_persen' => ['required', 'integer', 'min:0', 'max:100'],
            'minimal_poin_tukar' => ['required', 'integer', 'min:0'],
            'berlaku_mulai' => ['required', 'string', 'max:100'],
            'label_periode' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Pesan validasi berbahasa Indonesia yang jelas untuk admin.
     */
    public function messages(): array
    {
        return [
            'nilai_rupiah_per_poin.required' => 'Nilai rupiah per 1 poin wajib diisi.',
            'nilai_rupiah_per_poin.integer' => 'Nilai rupiah per 1 poin harus berupa angka bulat.',
            'nilai_rupiah_per_poin.min' => 'Nilai rupiah per 1 poin minimal Rp 1.',
            'batas_maksimal_potongan_persen.required' => 'Batas maksimal potongan wajib diisi.',
            'batas_maksimal_potongan_persen.integer' => 'Batas maksimal potongan harus berupa angka persentase bulat.',
            'batas_maksimal_potongan_persen.min' => 'Batas maksimal potongan tidak boleh bernilai negatif.',
            'batas_maksimal_potongan_persen.max' => 'Batas maksimal potongan tidak boleh melebihi 100%.',
            'minimal_poin_tukar.required' => 'Minimal poin untuk ditukarkan wajib diisi.',
            'minimal_poin_tukar.integer' => 'Minimal poin harus berupa angka bulat.',
            'minimal_poin_tukar.min' => 'Minimal poin tidak boleh bernilai negatif.',
            'berlaku_mulai.required' => 'Tanggal atau periode mulai berlaku wajib ditentukan.',
            'berlaku_mulai.max' => 'Keterangan tanggal mulai berlaku maksimal 100 karakter.',
            'label_periode.max' => 'Label periode maksimal 100 karakter.',
        ];
    }
}
