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
        Schema::create('denunciations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_pelapor_id')->constrained('users');
            $table->foreignId('type_denunciation_id')->constrained('denunciations');

            $table->text('alamat');
            $table->string('kecamatan_id');
            $table->string('kecamatan');
            $table->string('kelurahan_id');
            $table->string('kelurahan');

            $table->string('longitude');
            $table->string('latitude');

            $table->text('catatan')->nullable();

            $table->string('state')->default('sent');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('denunciations');
    }
};
