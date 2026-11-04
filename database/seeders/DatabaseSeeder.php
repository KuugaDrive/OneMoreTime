<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Urutan seeder disusun secara hierarkis menghormati foreign key constraints:
     * 1. StatusSeeder (statuses) -> dibutuhkan oleh users
     * 2. UserSeeder (users) -> dibutuhkan oleh packing_lists, operasionals, komisis
     * 3. CabangSeeder (cabangs)
     * 4. BebanOpsSeeder (beban_ops) -> dibutuhkan oleh operasionals
     * 5. PackingListSeeder (packing_lists)
     * 6. OperasionalSeeder (operasionals)
     * 7. KomisiSeeder (komisis)
     */
    public function run(): void
    {
        $this->call([
            StatusSeeder::class,
            UserSeeder::class,
            CabangSeeder::class,
            BebanOpsSeeder::class,
            PackingListSeeder::class,
            OperasionalSeeder::class,
            KomisiSeeder::class,
        ]);
    }
}
