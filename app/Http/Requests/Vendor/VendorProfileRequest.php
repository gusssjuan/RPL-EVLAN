<?php

namespace App\Http\Requests\Vendor;

use App\Enums\KategoriVendor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VendorProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isVendor() ?? false;
    }

    public function rules(): array
    {
        return [
            'nama_vendor' => ['required', 'string', 'max:255'],
            'kategori_vendor' => ['required', Rule::enum(KategoriVendor::class)],
            'alamat' => ['required', 'string', 'max:1000'],
            'kota' => ['nullable', 'string', 'max:100'],
            'kontak' => ['required', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_vendor.required' => 'Nama usaha wajib diisi.',
            'kategori_vendor.required' => 'Kategori vendor wajib dipilih.',
            'kategori_vendor.enum' => 'Kategori vendor yang dipilih tidak valid.',
            'alamat.required' => 'Alamat usaha wajib diisi.',
            'kontak.required' => 'Kontak usaha wajib diisi.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_vendor' => 'nama usaha',
            'kategori_vendor' => 'kategori vendor',
            'alamat' => 'alamat usaha',
            'kota' => 'kota',
            'kontak' => 'kontak',
            'deskripsi' => 'deskripsi usaha',
        ];
    }
}
