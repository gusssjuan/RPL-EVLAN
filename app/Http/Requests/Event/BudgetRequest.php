<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isUser() ?? false;
    }

    public function rules(): array
    {
        $eventId = $this->route('event')?->id;

        return [
            'nama_item' => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'harga_satuan' => ['required', 'integer', 'min:0'],
            'checklist_id' => [
                'nullable',
                'integer',
                Rule::exists('checklists', 'id')->where('event_id', $eventId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_item.required' => 'Nama item anggaran wajib diisi.',
            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.integer' => 'Jumlah harus berupa angka bulat.',
            'jumlah.min' => 'Jumlah minimal 1.',
            'harga_satuan.required' => 'Harga satuan wajib diisi.',
            'harga_satuan.integer' => 'Harga satuan harus berupa angka bulat rupiah.',
            'harga_satuan.min' => 'Harga satuan tidak boleh negatif.',
            'checklist_id.exists' => 'Kebutuhan yang dipilih tidak berasal dari acara ini.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_item' => 'nama item',
            'jumlah' => 'jumlah',
            'harga_satuan' => 'harga satuan',
            'checklist_id' => 'kebutuhan terkait',
        ];
    }
}
