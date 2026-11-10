<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class VendorProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isVendor() ?? false;
    }

    public function rules(): array
    {
        return [
            'nama_produk' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'integer', 'min:0'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'is_aktif' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_produk.required' => 'Nama produk/jasa wajib diisi.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.integer' => 'Harga harus berupa angka bulat rupiah.',
            'harga.min' => 'Harga tidak boleh negatif.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_produk' => 'nama produk/jasa',
            'harga' => 'harga',
            'deskripsi' => 'deskripsi',
            'is_aktif' => 'status aktif',
        ];
    }
}
