<?php

namespace App\Http\Requests\Discovery;

use Illuminate\Foundation\Http\FormRequest;

class CariAcaraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() === null || $this->user()->isUser();
    }

    protected function prepareForValidation(): void
    {
        $kataKunci = $this->input('cari', $this->input('q'));

        $this->merge([
            'cari' => filled($kataKunci) ? trim((string) $kataKunci) : null,
            'kategori' => filled($this->input('kategori')) && $this->input('kategori') !== 'semua'
                ? $this->input('kategori')
                : null,
            'lokasi' => filled($this->input('lokasi')) ? trim((string) $this->input('lokasi')) : null,
            'dari' => filled($this->input('dari')) ? $this->input('dari') : null,
            'sampai' => filled($this->input('sampai')) ? $this->input('sampai') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'cari' => ['nullable', 'string', 'max:100'],
            'kategori' => ['nullable', 'integer', 'exists:categories,id'],
            'lokasi' => ['nullable', 'string', 'max:100'],
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        ];
    }

    public function messages(): array
    {
        return [
            'sampai.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal mulai.',
        ];
    }

    public function attributes(): array
    {
        return [
            'cari' => 'kata kunci',
            'kategori' => 'kategori',
            'lokasi' => 'lokasi',
            'dari' => 'tanggal mulai',
            'sampai' => 'tanggal akhir',
        ];
    }
}
