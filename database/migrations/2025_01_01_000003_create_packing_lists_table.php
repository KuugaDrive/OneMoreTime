<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('packing_lists', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->string('kode_trx_pl', 20)->primary();
            $table->date('tanggal_pl')->index();
            $table->decimal('total', 15, 2);
            $table->decimal('discount_charge', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);
            $table->unsignedInteger('jumlah_paket');
            $table->text('ket_trx_pl')->nullable();
            $table->string('created_by', 10);
            $table->foreign('created_by')->references('id_user')->on('users')->restrictOnDelete();
            $table->timestamps();

            // TODO: relasi cabang (id_cab) belum ditambahkan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packing_lists');
    }
};
