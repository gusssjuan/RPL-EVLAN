<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'nama_template' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'is_aktif' => ['nullable', 'boolean'],
            'kebutuhan' => ['nullable', 'array'],
            'timeline' => ['nullable', 'array'],
            'timeline.*.nama_item' => ['nullable', 'string', 'max:255'],
            'timeline.*.offset_hari' => ['nullable', 'integer', 'min:0'],
            'divisi' => ['nullable', 'array'],
            'jobdesk' => ['nullable', 'array'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_template' => 'nama template',
            'category_id' => 'kategori',
            'timeline.*.offset_hari' => 'offset hari (H-)',
        ];
    }
}
