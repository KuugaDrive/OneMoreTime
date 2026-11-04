<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // PASSWORD DEFAULT, WAJIB DIGANTI sebelum dipakai sungguhan
        $defaultPassword = Hash::make('password');

        $users = [
            [
                'id_user' => 'USR-001',
                'username' => 'owner_utama',
                'no_hp' => '081234567890',
                'alamat' => 'Jl. Tomang Raya No. 12, Jakarta Barat',
                'status_id' => 'ST-001',
                'role' => 'owner',
                'id_cab' => null,
                'password' => $defaultPassword,
                'remember_token' => null,
            ],
            [
                'id_user' => 'USR-002',
                'username' => 'admin_operasional',
                'no_hp' => '081398765432',
                'alamat' => 'Jl. Kebon Jeruk No. 45, Jakarta Barat',
                'status_id' => 'ST-001',
                'role' => 'admin',
                'id_cab' => 'CB-001',
                'password' => $defaultPassword,
                'remember_token' => null,
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['id_user' => $user['id_user']],
                array_merge($user, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
