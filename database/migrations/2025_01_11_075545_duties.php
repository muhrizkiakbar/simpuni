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
        Schema::create('duties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_pelapor_id')->constrained('users');
            $table->foreignId('user_admin_id')->constrained('users');

            $table->string('state_type');

            $table->dateTime('tanggal_pengantaran');

            $table->text('nomor_bangunan');
            $table->text('catatan');

            $table->string('state')->default('on_going');
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
        Schema::dropIfExists('duties');
    }
};
