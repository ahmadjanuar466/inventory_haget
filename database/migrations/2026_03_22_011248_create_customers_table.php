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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 15)->unique();
            $table->string('name', 150);
            $table->string('phone', 16)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('address', 1509)->nullable();
            $table->foreignId('customer_type_id')->constrained('customer_types')->cascadeOnDelete();
            $table->integer('is_active')->unsigned()->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
