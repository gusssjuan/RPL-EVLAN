<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class StoreBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // otorisasi sudah via Policy di controller
    }

    public function rules(): array
    {
        return [
            'nama_item' => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'harga_satuan' => ['required', 'integer', 'min:0'],
            'checklist_id' => ['nullable', 'exists:checklists,id'],
            'vendor_product_id' => ['nullable', 'exists:vendor_products,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_item' => 'nama item',
            'jumlah' => 'jumlah',
            'harga_satuan' => 'harga satuan',
        ];
    }
}
