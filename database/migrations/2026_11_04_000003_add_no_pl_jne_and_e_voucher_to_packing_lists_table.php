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
        Schema::table('packing_lists', function (Blueprint $table) {
            $table->string('no_pl_jne', 30)->nullable()->unique()->after('kode_trx_pl');
            $table->decimal('e_voucher', 15, 2)->default(0)->after('discount_charge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packing_lists', function (Blueprint $table) {
            $table->dropUnique(['no_pl_jne']);
            $table->dropColumn(['no_pl_jne', 'e_voucher']);
        });
    }
};
