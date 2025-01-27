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
            $table->unsignedBigInteger('function_building_id');
            $table->foreign('function_building_id')->references('id')->on('function_buildings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('denunciations', function (Blueprint $table) {
            $table->dropForeign(['function_building_id']);
            $table->dropColumn('function_building_id');
        });
    }
};
