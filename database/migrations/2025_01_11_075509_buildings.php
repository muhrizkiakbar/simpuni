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
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('function_building_id')->constrained('function_buildings');
            $table->foreignId('user_admin_id')->nullable()->constrained('users');
            $table->foreignId('user_superadmin_id')->nullable()->constrained('users');

            $table->string('nomor_bangunan')->unique()->nullable();
            $table->string('name');
            $table->text('alamat');
            $table->string('kecamatan_id');
            $table->string('kecamatan');
            $table->string('kelurahan_id');
            $table->string('kelurahan');
            $table->integer('luas_bangunan');
            $table->integer('banyak_lantai');
            $table->integer('ketinggian');
            $table->string('longitude');
            $table->string('latitude');
            $table->string('state')->default('active');
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('buildings');
    }
};
