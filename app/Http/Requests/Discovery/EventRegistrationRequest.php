<?php

namespace App\Http\Requests\Discovery;

use Illuminate\Foundation\Http\FormRequest;

class EventRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isUser() ?? false;
    }

    public function rules(): array
    {
        return [
            'no_telepon' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^(?=(?:\D*\d){10,})[0-9+()\-\s]{10,20}$/',
            ],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'persetujuan_data' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'no_telepon.regex' => 'Nomor telepon minimal 10 digit dan hanya boleh berisi angka, spasi, +, -, atau tanda kurung.',
            'persetujuan_data.accepted' => 'Persetujuan penggunaan data wajib dicentang.',
            'catatan.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'no_telepon' => 'nomor telepon',
            'catatan' => 'catatan',
            'persetujuan_data' => 'persetujuan penggunaan data',
        ];
    }
}
