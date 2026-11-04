<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BebanOpsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opsList = [
            [
                'id_ops' => 'OP-001',
                'beban_ops' => 'Listrik & Air Kantor',
                'ket_ops' => 'Tagihan utilitas PLN & PAM bulanan kantor operasional',
            ],
            [
                'id_ops' => 'OP-002',
                'beban_ops' => 'Bahan Bakar & Tol Armada',
                'ket_ops' => 'Bensin pertalite/solar dan uang tol kurir pick up harian',
            ],
            [
                'id_ops' => 'OP-003',
                'beban_ops' => 'Lakban & Perlengkapan Packing',
                'ket_ops' => 'Pembelian lakban cokelat, bubble wrap, plastik wrapping, kardus',
            ],
            [
                'id_ops' => 'OP-004',
                'beban_ops' => 'Konsumsi & Lembur Karyawan',
                'ket_ops' => 'Biaya makan malam staf sortasi saat overload malam hari',
            ],
            [
                'id_ops' => 'OP-005',
                'beban_ops' => 'Perawatan Kendaraan & Servis',
                'ket_ops' => 'Ganti oli, servis berkala motor pickup dan mobil van',
            ],
        ];

        foreach ($opsList as $ops) {
            DB::table('beban_ops')->updateOrInsert(
                ['id_ops' => $ops['id_ops']],
                array_merge($ops, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
