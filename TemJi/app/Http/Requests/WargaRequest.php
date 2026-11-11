<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WargaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'wilayah_id' => ['required', Rule::exists('wilayah', 'id')->where('is_aktif', true)],
        ];
    }

    public function attributes(): array
    {
        return ['wilayah_id' => 'wilayah'];
    }
}
