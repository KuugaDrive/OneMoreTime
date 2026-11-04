<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pastikan tidak ada duplikasi sebelum update
        $hasOperasional = DB::table('users')->where('username', 'admin_operasional')->exists();
        if (!$hasOperasional) {
            DB::table('users')
                ->where('username', 'admin_counter')
                ->update(['username' => 'admin_operasional']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversible jika diperlukan
    }
};
