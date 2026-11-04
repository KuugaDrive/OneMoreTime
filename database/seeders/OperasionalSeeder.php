<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OperasionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $operasionals = [
            [
                'kode_trx_ops' => 'OPS-20250106-001',
                'id_cab' => 'CB-001',
                'tanggal_trx_ops' => '2025-01-06',
                'beban_ops_id' => 'OP-001',
                'nominal' => 450000.00,
                'ket_trx_ops' => 'Bayar token PLN kantor cabang Tomang',
                'created_by' => 'USR-001',
            ],
            [
                'kode_trx_ops' => 'OPS-20250114-002',
                'id_cab' => 'CB-002',
                'tanggal_trx_ops' => '2025-01-14',
                'beban_ops_id' => 'OP-002',
                'nominal' => 320000.00,
                'ket_trx_ops' => 'Bensin motor kurir minggu ke-2',
                'created_by' => 'USR-001',
            ],
            [
                'kode_trx_ops' => 'OPS-20250125-003',
                'id_cab' => 'CB-001',
                'tanggal_trx_ops' => '2025-01-25',
                'beban_ops_id' => 'OP-003',
                'nominal' => 275000.00,
                'ket_trx_ops' => 'Beli 2 dus lakban cokelat + 1 roll bubble wrap',
                'created_by' => 'USR-001',
            ],
            [
                'kode_trx_ops' => 'OPS-20250208-004',
                'id_cab' => 'CB-002',
                'tanggal_trx_ops' => '2025-02-08',
                'beban_ops_id' => 'OP-005',
                'nominal' => 350000.00,
                'ket_trx_ops' => 'Ganti oli dan kampas rem motor operasional B 3456 TMG',
                'created_by' => 'USR-001',
            ],
            [
                'kode_trx_ops' => 'OPS-20250220-005',
                'id_cab' => 'CB-001',
                'tanggal_trx_ops' => '2025-02-20',
                'beban_ops_id' => 'OP-004',
                'nominal' => 180000.00,
                'ket_trx_ops' => 'Makan malam lembur tim sortir banjir kiriman',
                'created_by' => 'USR-001',
            ],
            [
                'kode_trx_ops' => 'OPS-20250302-006',
                'id_cab' => 'CB-002',
                'tanggal_trx_ops' => '2025-03-02',
                'beban_ops_id' => 'OP-001',
                'nominal' => 470000.00,
                'ket_trx_ops' => 'Tagihan PAM & Listrik awal Maret',
                'created_by' => 'USR-001',
            ],
        ];

        foreach ($operasionals as $ops) {
            DB::table('operasionals')->updateOrInsert(
                ['kode_trx_ops' => $ops['kode_trx_ops']],
                array_merge($ops, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
