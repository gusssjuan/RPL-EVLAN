<?php

namespace App\Http\Requests\Auth;

use App\Enums\KategoriVendor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipe' => ['required', 'string', 'in:user,vendor'],
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'no_telepon' => ['nullable', 'string', 'max:20'],

            // Bidang khusus Vendor
            'nama_vendor' => ['required_if:tipe,vendor', 'nullable', 'string', 'max:255'],
            'kategori_vendor' => ['required_if:tipe,vendor', 'nullable', 'string', Rule::enum(KategoriVendor::class)],
            'alamat' => ['required_if:tipe,vendor', 'nullable', 'string'],
            'kontak' => ['required_if:tipe,vendor', 'nullable', 'string', 'max:50'],
        ];
    }

    public function attributes(): array
    {
        return [
            'tipe' => 'tipe akun',
            'nama' => 'nama lengkap',
            'email' => 'alamat email',
            'password' => 'kata sandi',
            'no_telepon' => 'nomor telepon',
            'nama_vendor' => 'nama usaha vendor',
            'kategori_vendor' => 'kategori vendor',
            'alamat' => 'alamat usaha',
            'kontak' => 'kontak usaha',
        ];
    }
}

