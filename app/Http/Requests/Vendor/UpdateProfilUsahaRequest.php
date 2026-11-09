<?php

namespace App\Http\Requests\Vendor;

use App\Enums\KategoriVendor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfilUsahaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->vendor !== null;
    }

    public function rules(): array
    {
        return [
            'nama_vendor' => ['required', 'string', 'max:255'],
            'kategori_vendor' => ['required', Rule::enum(KategoriVendor::class)],
            'alamat' => ['required', 'string', 'max:500'],
            'kota' => ['nullable', 'string', 'max:100'],
            'kontak' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_vendor' => 'nama usaha',
            'kategori_vendor' => 'kategori vendor',
            'alamat' => 'alamat',
            'kota' => 'kota',
            'kontak' => 'kontak',
            'deskripsi' => 'deskripsi',
        ];
    }
}
