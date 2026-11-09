<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_item' => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'harga_satuan' => ['required', 'integer', 'min:0'],
            'checklist_id' => ['nullable', 'exists:checklists,id'],
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
