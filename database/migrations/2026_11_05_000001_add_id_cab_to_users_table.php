<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom id_cab nullable dengan index
        Schema::table('users', function (Blueprint $table) {
            $table->string('id_cab', 10)->nullable()->after('role')->index();
        });

        // 2. Backfill: cari cabang pertama berdasarkan urutan id_cab
        $firstCabang = DB::table('cabangs')->orderBy('id_cab', 'asc')->first();
        if (!$firstCabang) {
            throw new \RuntimeException('Tabel cabangs kosong. Migrasi dihentikan karena tidak dapat melakukan backfill id_cab untuk admin.');
        }

        // Isi semua user ber-role admin yang id_cab-nya masih NULL dengan cabang pertama
        DB::table('users')
            ->where('role', 'admin')
            ->whereNull('id_cab')
            ->update(['id_cab' => $firstCabang->id_cab]);

        // Pastikan user owner tetap NULL
        DB::table('users')
            ->where('role', 'owner')
            ->update(['id_cab' => null]);

        // 3. Tambahkan foreign key constraint (restrictOnDelete)
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('id_cab')
                ->references('id_cab')
                ->on('cabangs')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_cab']);
            $table->dropColumn('id_cab');
        });
    }
};
