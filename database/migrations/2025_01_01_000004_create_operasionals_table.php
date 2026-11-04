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
        Schema::create('operasionals', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->string('kode_trx_ops', 20)->primary();
            $table->date('tanggal_trx_ops')->index();
            $table->string('beban_ops_id', 10);
            $table->foreign('beban_ops_id')->references('id_ops')->on('beban_ops')->restrictOnDelete();
            $table->decimal('nominal', 15, 2);
            $table->text('ket_trx_ops')->nullable();
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
        Schema::dropIfExists('operasionals');
    }
};
