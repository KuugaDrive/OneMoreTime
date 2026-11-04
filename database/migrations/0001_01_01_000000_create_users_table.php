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
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->string('id_user', 10)->primary();
            $table->string('username', 50)->unique();
            $table->string('no_hp', 20);
            $table->text('alamat');
            $table->string('status_id', 10);
            $table->foreign('status_id')->references('id_status')->on('statuses')->restrictOnDelete();
            $table->enum('role', ['owner', 'admin']);
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
