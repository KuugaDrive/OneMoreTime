<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Pastikan minimal ada satu cabang untuk backfill data lama
        $firstCab = DB::table('cabangs')->orderBy('id_cab')->first();
        if (!$firstCab) {
            DB::table('cabangs')->insert([
                'id_cab' => 'CB-001',
                'nama_cabang' => 'Cabang Utama',
                'alamat' => 'Jl. Tomang Raya No. 11, Jakarta Barat',
                'nama_pic' => 'Manager Utama',
                'no_hp' => '081234567890',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $defaultCabId = 'CB-001';
        } else {
            $defaultCabId = $firstCab->id_cab;
        }

        // 2. Tabel packing_lists: tambah kolom nullable, backfill, lalu set NOT NULL + FK + index
        Schema::table('packing_lists', function (Blueprint $table) {
            $table->string('id_cab', 10)->nullable()->after('kode_trx_pl')->index();
        });
        DB::table('packing_lists')->whereNull('id_cab')->update(['id_cab' => $defaultCabId]);
        Schema::table('packing_lists', function (Blueprint $table) {
            $table->string('id_cab', 10)->nullable(false)->change();
            $table->foreign('id_cab')->references('id_cab')->on('cabangs')->restrictOnDelete();
        });

        // 3. Tabel operasionals: tambah kolom nullable, backfill, lalu set NOT NULL + FK + index
        Schema::table('operasionals', function (Blueprint $table) {
            $table->string('id_cab', 10)->nullable()->after('kode_trx_ops')->index();
        });
        DB::table('operasionals')->whereNull('id_cab')->update(['id_cab' => $defaultCabId]);
        Schema::table('operasionals', function (Blueprint $table) {
            $table->string('id_cab', 10)->nullable(false)->change();
            $table->foreign('id_cab')->references('id_cab')->on('cabangs')->restrictOnDelete();
        });

        // 4. Tabel komisis: tambah kolom nullable, backfill, lalu set NOT NULL + FK + index
        Schema::table('komisis', function (Blueprint $table) {
            $table->string('id_cab', 10)->nullable()->after('kode_trx_cl')->index();
        });
        DB::table('komisis')->whereNull('id_cab')->update(['id_cab' => $defaultCabId]);
        Schema::table('komisis', function (Blueprint $table) {
            $table->string('id_cab', 10)->nullable(false)->change();
            $table->foreign('id_cab')->references('id_cab')->on('cabangs')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packing_lists', function (Blueprint $table) {
            $table->dropForeign(['id_cab']);
            $table->dropColumn('id_cab');
        });

        Schema::table('operasionals', function (Blueprint $table) {
            $table->dropForeign(['id_cab']);
            $table->dropColumn('id_cab');
        });

        Schema::table('komisis', function (Blueprint $table) {
            $table->dropForeign(['id_cab']);
            $table->dropColumn('id_cab');
        });
    }
};
