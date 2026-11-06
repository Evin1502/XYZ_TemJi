<?php

namespace App\Http\Requests;

use App\Models\KategoriDaurUlang;
use Illuminate\Foundation\Http\FormRequest;

class StorePengajuanDaurUlangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $rawItems = $this->input('items', []);
        $selectedCategories = $this->input('kategori_id', []);
        $weights = $this->input('berat_kg', []);

        $normalized = [];

        // Format 1: items = [['kategori_id' => 1, 'berat_kg' => 2], ...] atau items[kId] = ['berat_kg' => 2, ...]
        if (is_array($rawItems) && !empty($rawItems)) {
            foreach ($rawItems as $key => $val) {
                if (is_array($val)) {
                    $kId = $val['kategori_id'] ?? $key;
                    $berat = $val['berat_kg'] ?? $val['berat'] ?? null;
                    $selected = $val['selected'] ?? true;
                    if ($selected && $kId && $berat !== null) {
                        $normalized[] = [
                            'kategori_id' => (int) $kId,
                            'berat_kg' => (int) $berat,
                        ];
                    }
                } elseif (is_numeric($key) && is_numeric($val)) {
                    $normalized[] = [
                        'kategori_id' => (int) $key,
                        'berat_kg' => (int) $val,
                    ];
                }
            }
        }

        // Format 2: kategori_id[] array + berat_kg[id]
        if (empty($normalized) && is_array($selectedCategories)) {
            foreach ($selectedCategories as $kId) {
                $kId = (int) $kId;
                $berat = is_array($weights) ? ($weights[$kId] ?? 1) : 1;
                $normalized[] = [
                    'kategori_id' => $kId,
                    'berat_kg' => (int) $berat,
                ];
            }
        }

        $this->merge([
            'selected_items' => $normalized,
        ]);
    }

    public function rules(): array
    {
        return [
            'selected_items' => ['required', 'array', 'min:1'],
            'selected_items.*.kategori_id' => ['required', 'integer', 'exists:kategori_daur_ulang,id'],
            'selected_items.*.berat_kg' => ['required', 'integer', 'min:1', 'max:500'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'selected_items.required' => 'Pilih minimal satu kategori daur ulang yang akan disetor.',
            'selected_items.min' => 'Pilih minimal satu kategori daur ulang yang akan disetor.',
            'selected_items.*.kategori_id.exists' => 'Kategori daur ulang tidak valid.',
            'selected_items.*.berat_kg.min' => 'Perkiraan berat setiap kategori minimal 1 kg.',
            'selected_items.*.berat_kg.required' => 'Perkiraan berat wajib diisi.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->input('selected_items', []);
            if (!empty($items)) {
                $categoryIds = array_column($items, 'kategori_id');
                $activeCount = KategoriDaurUlang::whereIn('id', $categoryIds)
                    ->where('is_active', true)
                    ->count();

                if ($activeCount !== count(array_unique($categoryIds))) {
                    $validator->errors()->add('selected_items', 'Kategori daur ulang tidak valid atau sedang nonaktif.');
                }
            }
        });
    }
}
