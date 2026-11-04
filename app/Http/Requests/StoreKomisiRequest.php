<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\NumberParser;
use InvalidArgumentException;

class StoreKomisiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_cab' => 'required|string|exists:cabangs,id_cab',
            'tanggal_tf' => 'required',
            'nominal' => 'required',
            'ket_trx_cl' => 'required|string',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            try {
                $nominal = NumberParser::parse($this->nominal);
                if ($nominal < 0) {
                    $validator->errors()->add('nominal', 'Nominal transfer komisi tidak boleh bernilai negatif.');
                }
            } catch (InvalidArgumentException $e) {
                $validator->errors()->add('nominal', 'Format angka tidak dikenali pada Nominal transfer komisi.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'id_cab.required' => 'Cabang wajib dipilih.',
            'id_cab.exists' => 'Cabang yang dipilih tidak valid di sistem.',
            'tanggal_tf.required' => 'Tanggal transfer komisi wajib diisi.',
            'nominal.required' => 'Nominal transfer komisi wajib diisi.',
            'ket_trx_cl.required' => 'Keterangan transaksi komisi wajib diisi.',
        ];
    }
}
