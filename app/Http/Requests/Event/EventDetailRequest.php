<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class EventDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isUser() ?? false;
    }

    public function rules(): array
    {
        return [
            'nama_event' => ['required', 'string', 'max:255'],
            'tujuan' => ['required', 'string'],
            'tanggal' => ['required', 'date', 'after_or_equal:today'],
            'waktu_mulai' => ['nullable', 'date_format:H:i'],
            'waktu_selesai' => ['nullable', 'date_format:H:i'],
            'lokasi' => ['required', 'string', 'max:255'],
            'target_peserta' => ['required', 'string', 'max:255'],
            'jumlah_peserta' => ['required', 'integer', 'min:1'],
            'is_publik' => ['nullable', 'boolean'],
            'pendaftaran_dibuka' => ['nullable', 'boolean'],
            'batas_pendaftaran' => ['nullable', 'date'],
            'kuota' => ['nullable', 'integer', 'min:1'],
            'penyelenggara' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.after_or_equal' => 'Tanggal tidak boleh sebelum hari ini.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_event' => 'nama acara',
            'tujuan' => 'tujuan acara',
            'tanggal' => 'tanggal acara',
            'waktu_mulai' => 'waktu mulai',
            'waktu_selesai' => 'waktu selesai',
            'lokasi' => 'lokasi acara',
            'target_peserta' => 'target peserta',
            'jumlah_peserta' => 'jumlah peserta',
        ];
    }
}

