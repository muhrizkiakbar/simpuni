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
            $table->string('catatan_reject')->nullable(); // Store the file path
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('denunciations', function (Blueprint $table) {
            $table->dropColumn('catatan_reject');
        });
    }
};
