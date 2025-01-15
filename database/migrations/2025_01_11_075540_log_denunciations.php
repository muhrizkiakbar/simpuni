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
        //
        //
        Schema::create('log_denunciations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('denunciation_id')->constrained('denunciations');
            $table->foreignId('user_admin_id')->constrained('users');

            $table->string('current_state');
            $table->string('new_state');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('log_denunciations');
    }
};
