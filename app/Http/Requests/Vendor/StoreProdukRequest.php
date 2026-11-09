<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class StoreProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->vendor !== null;
    }

    public function rules(): array
    {
        return [
            'nama_produk' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'integer', 'min:0'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'is_aktif' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_produk' => 'nama produk/jasa',
            'harga' => 'harga',
            'deskripsi' => 'deskripsi',
        ];
    }
}
