<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class TolakPendaftaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isUser() ?? false;
    }

    public function rules(): array
    {
        return [
            'alasan_penolakan' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi.',
            'alasan_penolakan.min' => 'Alasan penolakan minimal 5 karakter.',
            'alasan_penolakan.max' => 'Alasan penolakan maksimal 500 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'alasan_penolakan' => 'alasan penolakan',
        ];
    }
}
