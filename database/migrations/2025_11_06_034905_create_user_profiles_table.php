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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap', 150);
            $table->string('email')->nullable();
            $table->string('alamat', 150)->nullable();
            $table->string('no_telp', 15)->nullable();
            $table->string('avatars', 50)->nullable();
            $table->date('birth_date')->nullable();
            $table->foreignId('users_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
