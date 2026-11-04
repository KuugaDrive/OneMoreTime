<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackingListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packingLists = [
            [
                'kode_trx_pl' => 'PL-20250105-001',
                'no_pl_jne' => 'PL/17/2510001001',
                'id_cab' => 'CB-001',
                'tanggal_pl' => '2025-01-05',
                'total' => 2450000.00,
                'discount_charge' => 125000.00,
                'e_voucher' => 0.00,
                'jumlah_paket' => 142,
                'ket_trx_pl' => 'Pengiriman barang reguler & YES sesi pagi-sore',
                'created_by' => 'USR-001',
            ],
            [
                'kode_trx_pl' => 'PL-20250112-002',
                'no_pl_jne' => 'PL/17/2510001002',
                'id_cab' => 'CB-002',
                'tanggal_pl' => '2025-01-12',
                'total' => 3100000.00,
                'discount_charge' => 150000.00,
                'e_voucher' => 25000.00,
                'jumlah_paket' => 185,
                'ket_trx_pl' => 'Pengiriman e-commerce weekend promo voucher',
                'created_by' => 'USR-002',
            ],
            [
                'kode_trx_pl' => 'PL-20250120-003',
                'no_pl_jne' => 'PL/17/2510001003',
                'id_cab' => 'CB-001',
                'tanggal_pl' => '2025-01-20',
                'total' => 1850000.00,
                'discount_charge' => 95000.00,
                'e_voucher' => 0.00,
                'jumlah_paket' => 110,
                'ket_trx_pl' => 'Pick up marketplace Tokopedia & Shopee',
                'created_by' => 'USR-001',
            ],
            [
                'kode_trx_pl' => 'PL-20250203-004',
                'no_pl_jne' => 'PL/17/2510001004',
                'id_cab' => 'CB-002',
                'tanggal_pl' => '2025-02-03',
                'total' => 2800000.00,
                'discount_charge' => 140000.00,
                'e_voucher' => 50000.00,
                'jumlah_paket' => 165,
                'ket_trx_pl' => 'Awal bulan lonjakan pengiriman gajian',
                'created_by' => 'USR-002',
            ],
            [
                'kode_trx_pl' => 'PL-20250215-005',
                'no_pl_jne' => 'PL/17/2510001005',
                'id_cab' => 'CB-001',
                'tanggal_pl' => '2025-02-15',
                'total' => 3450000.00,
                'discount_charge' => 175000.00,
                'e_voucher' => 0.00,
                'jumlah_paket' => 210,
                'ket_trx_pl' => 'Event Valentine & Chinese New Year cargo',
                'created_by' => 'USR-001',
            ],
            [
                'kode_trx_pl' => 'PL-20250301-006',
                'no_pl_jne' => 'PL/17/2510001006',
                'id_cab' => 'CB-002',
                'tanggal_pl' => '2025-03-01',
                'total' => 2100000.00,
                'discount_charge' => 100000.00,
                'e_voucher' => 20000.00,
                'jumlah_paket' => 128,
                'ket_trx_pl' => 'Pengiriman normal awal bulan Maret',
                'created_by' => 'USR-002',
            ],
        ];

        foreach ($packingLists as $pl) {
            $grandTotal = $pl['total'] - $pl['discount_charge'] - $pl['e_voucher'];

            DB::table('packing_lists')->updateOrInsert(
                ['kode_trx_pl' => $pl['kode_trx_pl']],
                [
                    'kode_trx_pl' => $pl['kode_trx_pl'],
                    'no_pl_jne' => $pl['no_pl_jne'],
                    'id_cab' => $pl['id_cab'],
                    'tanggal_pl' => $pl['tanggal_pl'],
                    'total' => $pl['total'],
                    'discount_charge' => $pl['discount_charge'],
                    'e_voucher' => $pl['e_voucher'],
                    'grand_total' => $grandTotal,
                    'jumlah_paket' => $pl['jumlah_paket'],
                    'ket_trx_pl' => $pl['ket_trx_pl'],
                    'created_by' => $pl['created_by'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
