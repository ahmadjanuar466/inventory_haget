<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_receipts', function (Blueprint $table) {
            $table->string('invoice_file')->nullable()->after('invoice_no');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_receipts', function (Blueprint $table) {
            $table->dropColumn('invoice_file');
        });
    }
};
