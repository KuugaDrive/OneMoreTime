<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_status' => 'required|string|max:50|unique:statuses,nama_status',
            'level' => 'required|integer|in:1,2,3,4',
            'ket_status' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_status.required' => 'Nama status wajib diisi.',
            'nama_status.unique' => 'Nama status sudah terdaftar di sistem.',
            'level.required' => 'Level status wajib dipilih.',
            'level.in' => 'Level status harus antara 1 sampai 4.',
            'ket_status.required' => 'Keterangan status wajib diisi.',
        ];
    }
}
