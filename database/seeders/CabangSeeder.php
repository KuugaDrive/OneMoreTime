<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CabangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cabangs = [
            [
                'id_cab' => 'CB-001',
                'nama_cabang' => 'Cabang Tomang Raya',
                'alamat' => 'Jl. Tomang Raya No. 11, Jakarta Barat',
                'nama_pic' => 'Budi Santoso',
                'user_name_jne' => 'BASECLG',
                'no_hp' => '081122334455',
            ],
            [
                'id_cab' => 'CB-002',
                'nama_cabang' => 'Cabang Kebon Jeruk',
                'alamat' => 'Jl. Surya Mandala No. 8, Jakarta Barat',
                'nama_pic' => 'Siti Nurhaliza',
                'user_name_jne' => 'KBJ01',
                'no_hp' => '081233445566',
            ],
            [
                'id_cab' => 'CB-003',
                'nama_cabang' => 'Cabang Cengkareng Hub',
                'alamat' => 'Jl. Daan Mogot Km 14, Jakarta Barat',
                'nama_pic' => 'Hendra Setiawan',
                'user_name_jne' => null,
                'no_hp' => '081344556677',
            ],
            [
                'id_cab' => 'CB-004',
                'nama_cabang' => 'Cabang Meruya Selatan',
                'alamat' => 'Jl. Meruya Selatan No. 25, Jakarta Barat',
                'nama_pic' => 'Rina Wijayanti',
                'user_name_jne' => null,
                'no_hp' => '081455667788',
            ],
        ];

        foreach ($cabangs as $cabang) {
            DB::table('cabangs')->updateOrInsert(
                ['id_cab' => $cabang['id_cab']],
                array_merge($cabang, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
