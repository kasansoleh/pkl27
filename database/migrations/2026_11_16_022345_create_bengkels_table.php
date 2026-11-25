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
        Schema::create('bengkels', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bengkel');
            $table->string('bidang_keahlian')->default('Bubut & Machining Precision');
            $table->text('alamat');
            $table->string('nama_pemilik');
            $table->string('hp_pemilik');
            $table->integer('kuota')->default(3);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bengkels');
    }
};
