<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Support\NumberParser;
use InvalidArgumentException;

class StorePackingListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('no_pl_jne') && is_string($this->no_pl_jne)) {
            $val = strtoupper(trim($this->no_pl_jne));
            $this->merge([
                'no_pl_jne' => $val !== '' ? $val : null,
            ]);
        }

        // Bagian A.6: Admin backend selalu memakai id_cab milik akun yang login dan mengabaikan request
        $user = auth()->user();
        if ($user && $user->isAdmin()) {
            if (empty($user->id_cab)) {
                abort(403, 'Akun Anda belum terikat ke cabang. Hubungi Owner.');
            }
            $this->merge([
                'id_cab' => $user->id_cab,
            ]);
        }
    }

    public function rules(): array
    {
        $kodeTrx = $this->route('kode') ?? $this->kode_trx_pl;
        $isExisting = !empty($kodeTrx) && \App\Models\PackingList::where('kode_trx_pl', $kodeTrx)->exists();

        $noPlRules = ['string', 'max:30'];
        if ($isExisting) {
            $noPlRules[] = 'nullable';
            $noPlRules[] = Rule::unique('packing_lists', 'no_pl_jne')->ignore($kodeTrx, 'kode_trx_pl');
        } else {
            $noPlRules[] = 'required';
            $noPlRules[] = 'unique:packing_lists,no_pl_jne';
        }

        return [
            'no_pl_jne' => $noPlRules,
            'id_cab' => 'required|string|exists:cabangs,id_cab',
            'tanggal_pl' => 'required',
            'jumlah_paket' => 'required|integer|min:0',
            'total' => 'required',
            'discount_charge' => 'required',
            'e_voucher' => 'nullable',
            'ket_trx_pl' => 'required|string',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $total = null;
            $discount = null;
            $eVoucher = 0.0;

            try {
                $total = NumberParser::parse($this->total);
                if ($total < 0) {
                    $validator->errors()->add('total', 'Total penjualan tidak boleh bernilai negatif.');
                }
            } catch (InvalidArgumentException $e) {
                $validator->errors()->add('total', 'Format angka tidak dikenali pada Total penjualan.');
            }

            try {
                $discount = NumberParser::parse($this->discount_charge);
                if ($discount < 0) {
                    $validator->errors()->add('discount_charge', 'Discount charge tidak boleh bernilai negatif.');
                }
            } catch (InvalidArgumentException $e) {
                $validator->errors()->add('discount_charge', 'Format angka tidak dikenali pada Discount charge.');
            }

            if ($this->filled('e_voucher')) {
                try {
                    $eVoucher = NumberParser::parse($this->e_voucher);
                    if ($eVoucher < 0) {
                        $validator->errors()->add('e_voucher', 'e-Voucher tidak boleh bernilai negatif.');
                    }
                } catch (InvalidArgumentException $e) {
                    $validator->errors()->add('e_voucher', 'Format angka tidak dikenali pada e-Voucher.');
                }
            }

            if ($total !== null && $discount !== null) {
                if (($discount + $eVoucher) > $total) {
                    $validator->errors()->add('discount_charge', 'Jumlah Discount Charge dan e-Voucher tidak boleh lebih besar dari total penjualan.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'no_pl_jne.required' => 'Nomor PL JNE wajib diisi.',
            'no_pl_jne.unique' => 'Nomor PL JNE sudah pernah diinput.',
            'id_cab.required' => 'Cabang wajib dipilih.',
            'id_cab.exists' => 'Cabang yang dipilih tidak valid di sistem.',
            'tanggal_pl.required' => 'Tanggal packing list wajib diisi.',
            'jumlah_paket.required' => 'Jumlah paket wajib diisi.',
            'jumlah_paket.integer' => 'Jumlah paket harus berupa bilangan bulat.',
            'jumlah_paket.min' => 'Jumlah paket tidak boleh negatif.',
            'total.required' => 'Total penjualan wajib diisi.',
            'discount_charge.required' => 'Discount charge wajib diisi.',
            'ket_trx_pl.required' => 'Keterangan transaksi wajib diisi.',
        ];
    }
}
