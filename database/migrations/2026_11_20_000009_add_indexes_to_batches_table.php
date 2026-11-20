<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->index('lot_number');
            $table->index('expiry_date');
            $table->index(['product_id', 'expiry_date']); // FEFO composite
        });
    }

    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropIndex(['lot_number']);
            $table->dropIndex(['expiry_date']);
            $table->dropIndex(['product_id', 'expiry_date']);
        });
    }
};
