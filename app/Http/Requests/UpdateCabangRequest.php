<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCabangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('user_name_jne') && is_string($this->user_name_jne)) {
            $val = trim($this->user_name_jne);
            $this->merge([
                'user_name_jne' => $val !== '' ? $val : null,
            ]);
        }
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->id;

        return [
            'nama_cabang' => ['required', 'string', 'max:100', Rule::unique('cabangs', 'nama_cabang')->ignore($id, 'id_cab')],
            'alamat' => 'required|string',
            'nama_pic' => 'required|string|max:100',
            'user_name_jne' => ['nullable', 'string', 'max:50', Rule::unique('cabangs', 'user_name_jne')->ignore($id, 'id_cab')],
            'no_hp' => ['required', 'string', 'regex:/^[0-9]+$/', 'min:8', 'max:15'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_cabang.required' => 'Nama cabang wajib diisi.',
            'nama_cabang.unique' => 'Nama cabang sudah terdaftar di sistem.',
            'alamat.required' => 'Alamat cabang wajib diisi.',
            'nama_pic.required' => 'Nama penanggung jawab (PIC) wajib diisi.',
            'user_name_jne.unique' => 'User Name JNE sudah pernah digunakan oleh cabang lain.',
            'no_hp.required' => 'Nomor HP cabang wajib diisi.',
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka.',
            'no_hp.min' => 'Nomor HP minimal 8 digit angka.',
            'no_hp.max' => 'Nomor HP maksimal 15 digit angka.',
        ];
    }
}
