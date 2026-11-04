<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $role = strtolower($this->input('role') ?? $this->input('level') ?? '');
        $isRoleAdmin = ($role === 'admin');

        return [
            'nama_user' => 'required|string|max:50|unique:users,username',
            'no_hp' => ['required', 'string', 'regex:/^[0-9]+$/', 'min:8', 'max:15'],
            'alamat' => 'required|string',
            'status' => 'required|string',
            'role' => 'nullable|string|in:Owner,Admin,owner,admin',
            'level' => 'nullable|string|in:Owner,Admin,owner,admin',
            'id_cab' => $isRoleAdmin ? ['required', 'string', 'exists:cabangs,id_cab'] : ['nullable'],
            'password' => 'required|string|min:6',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->filled('role') && !$this->filled('level')) {
                $validator->errors()->add('role', 'Role pengguna wajib dipilih.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'nama_user.required' => 'Nama pengguna wajib diisi.',
            'nama_user.unique' => 'Nama pengguna / username sudah digunakan di sistem.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka.',
            'no_hp.min' => 'Nomor HP minimal 8 digit angka.',
            'no_hp.max' => 'Nomor HP maksimal 15 digit angka.',
            'alamat.required' => 'Alamat lengkap wajib diisi.',
            'status.required' => 'Status akun wajib dipilih.',
            'id_cab.required' => 'Cabang penugasan wajib dipilih untuk Admin.',
            'id_cab.exists' => 'Cabang yang dipilih tidak terdaftar di sistem.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ];
    }
}
