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
        Schema::table('denunciations', function (Blueprint $table) {
            $table->unsignedBigInteger('type_denunciation_id');
            $table->foreign('type_denunciation_id')->references('id')->on('type_denunciations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('denunciations', function (Blueprint $table) {
            $table->dropForeign(['type_denunciation_id']);
            $table->dropColumn('type_denunciation_id');
        });
    }
};
