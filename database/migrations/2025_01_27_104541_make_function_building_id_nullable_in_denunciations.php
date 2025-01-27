<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('denunciations', function (Blueprint $table) {
            $table->unsignedBigInteger('function_building_id')->nullable()->change(); // Modify column to be nullable
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('denunciations', function (Blueprint $table) {
            $table->unsignedBigInteger('function_building_id')->nullable(false)->change(); // Revert to non-nullable if rolled back
        });
    }
};
