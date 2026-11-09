<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $categoryId = $this->route('kategori') instanceof Category
            ? $this->route('kategori')->id
            : $this->route('kategori');

        return [
            'nama_kategori' => [
                'required',
                'string',
                'max:100',
                Rule::unique(Category::class, 'nama_kategori')->ignore($categoryId),
            ],
            'ikon' => ['nullable', 'string', 'max:8'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_kategori' => 'nama kategori',
            'ikon' => 'ikon / emoji',
            'deskripsi' => 'deskripsi kategori',
        ];
    }
}
