<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Support\NumberParser;
use InvalidArgumentException;

class StoreOperasionalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_cab' => 'required|string|exists:cabangs,id_cab',
            'tanggal_trx_ops' => 'required',
            'beban_ops' => 'required_without:beban_ops_id',
            'nominal' => 'required',
            'ket_trx_ops' => 'required|string',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            try {
                $nominal = NumberParser::parse($this->nominal);
                if ($nominal < 0) {
                    $validator->errors()->add('nominal', 'Nominal pengeluaran operasional tidak boleh bernilai negatif.');
                }
            } catch (InvalidArgumentException $e) {
                $validator->errors()->add('nominal', 'Format angka tidak dikenali pada Nominal biaya.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'id_cab.required' => 'Cabang wajib dipilih.',
            'id_cab.exists' => 'Cabang yang dipilih tidak valid di sistem.',
            'tanggal_trx_ops.required' => 'Tanggal transaksi operasional wajib diisi.',
            'beban_ops.required_without' => 'Beban Operasional wajib dipilih.',
            'nominal.required' => 'Nominal biaya wajib diisi.',
            'ket_trx_ops.required' => 'Keterangan pengeluaran operasional wajib diisi.',
        ];
    }
}
