<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JadwalkanTarifRequest extends FormRequest
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
            'poin_per_kg' => 'required|integer|min:1',
            'berlaku_mulai' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'poin_per_kg.required' => 'Tarif baru (poin per kg) wajib diisi.',
            'poin_per_kg.integer' => 'Tarif poin harus berupa bilangan bulat.',
            'poin_per_kg.min' => 'Tarif poin minimal 1 poin per kg.',
            'berlaku_mulai.required' => 'Tanggal berlaku mulai wajib diisi.',
            'berlaku_mulai.date' => 'Format tanggal tidak valid.',
        ];
    }
}
