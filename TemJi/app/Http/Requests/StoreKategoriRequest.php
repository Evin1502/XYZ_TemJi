<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKategoriRequest extends FormRequest
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
            'nama' => 'required|string|max:100',
            'tipe' => 'required|string|max:50',
            'satuan' => 'required|string|max:20',
            'icon' => 'nullable|string|in:droplet,bottle,cup',
            'poin_per_kg' => 'required|integer|min:1',
            'berlaku_sejak' => 'nullable|date',
            'deskripsi' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kategori wajib diisi.',
            'tipe.required' => 'Tipe kategori wajib dipilih.',
            'satuan.required' => 'Satuan kategori wajib diisi.',
            'poin_per_kg.required' => 'Poin per kg wajib diisi.',
            'poin_per_kg.integer' => 'Poin per kg harus berupa angka bulat.',
            'poin_per_kg.min' => 'Poin per kg minimal 1 poin.',
        ];
    }
}
