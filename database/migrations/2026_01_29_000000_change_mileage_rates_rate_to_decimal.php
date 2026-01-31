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
        // Change the column to decimal to allow fractional cents values
        Schema::table('mileage_rates', function (Blueprint $table) {
            // Using 8,2 should be more than enough precision for cents per mile
            $table->decimal('rate_cents_per_mile', 8, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mileage_rates', function (Blueprint $table) {
            $table->integer('rate_cents_per_mile')->change();
        });
    }
};
