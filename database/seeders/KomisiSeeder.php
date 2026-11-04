<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KomisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $komisis = [
            [
                'kode_trx_cl' => 'CL-20250131-001',
                'id_cab' => 'CB-001',
                'tanggal_tf' => '2025-01-31',
                'nominal' => 5250000.00,
                'ket_trx_cl' => 'Pencairan komisi kemitraan JNE periode Januari 2025',
                'created_by' => 'USR-001',
            ],
            [
                'kode_trx_cl' => 'CL-20250228-002',
                'id_cab' => 'CB-002',
                'tanggal_tf' => '2025-02-28',
                'nominal' => 6100000.00,
                'ket_trx_cl' => 'Pencairan komisi kemitraan JNE periode Februari 2025',
                'created_by' => 'USR-001',
            ],
            [
                'kode_trx_cl' => 'CL-20250331-003',
                'id_cab' => 'CB-001',
                'tanggal_tf' => '2025-03-31',
                'nominal' => 4900000.00,
                'ket_trx_cl' => 'Pencairan komisi kemitraan JNE periode Maret 2025',
                'created_by' => 'USR-001',
            ],
        ];

        foreach ($komisis as $komisi) {
            DB::table('komisis')->updateOrInsert(
                ['kode_trx_cl' => $komisi['kode_trx_cl']],
                array_merge($komisi, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
