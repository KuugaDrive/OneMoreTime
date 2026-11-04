<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Catatan Otorisasi:
     * Hanya pengguna dengan status Level 1 (Aktif) yang memiliki izin untuk login ke sistem.
     * Level 2 (Cuti), Level 3 (Suspend), dan Level 4 (Nonaktif) tidak diizinkan masuk.
     */
    public function run(): void
    {
        $statuses = [
            [
                'id_status' => 'ST-001',
                'nama_status' => 'Aktif',
                'level' => 1,
                'ket_status' => 'Status user atau operasional aktif dan diizinkan login',
            ],
            [
                'id_status' => 'ST-002',
                'nama_status' => 'Cuti',
                'level' => 2,
                'ket_status' => 'Status karyawan dalam masa cuti, login ditangguhkan',
            ],
            [
                'id_status' => 'ST-003',
                'nama_status' => 'Suspend',
                'level' => 3,
                'ket_status' => 'Status dibekukan karena pelanggaran SOP atau investigasi',
            ],
            [
                'id_status' => 'ST-004',
                'nama_status' => 'Nonaktif',
                'level' => 4,
                'ket_status' => 'Status akun dinonaktifkan dan tidak memiliki akses ke sistem',
            ],
        ];

        foreach ($statuses as $status) {
            DB::table('statuses')->updateOrInsert(
                ['id_status' => $status['id_status']],
                array_merge($status, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
