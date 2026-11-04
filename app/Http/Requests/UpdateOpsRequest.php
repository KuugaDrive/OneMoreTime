<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOpsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $idOps = $this->route('id') ?? $this->route('op') ?? $this->id_ops;

        return [
            'beban_ops' => [
                'required',
                'string',
                'max:100',
                Rule::unique('beban_ops', 'beban_ops')->ignore($idOps, 'id_ops'),
            ],
            'ket_ops' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'beban_ops.required' => 'Nama beban operasional wajib diisi.',
            'beban_ops.unique' => 'Nama beban operasional sudah terdaftar.',
            'beban_ops.max' => 'Nama beban operasional maksimal 100 karakter.',
            'ket_ops.required' => 'Keterangan operasional wajib diisi.',
            'ket_ops.max' => 'Keterangan operasional maksimal 255 karakter.',
        ];
    }
}
