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
        Schema::create('characters', function (Blueprint $table) {
            $table->id();

            // Jenis aksara: jawa, sunda, bali, batak, bugis, dll.
            $table->string('script_type', 50)->index();

            // Karakter aksara (Unicode)
            $table->string('character', 20);

            // Transliterasi Latin
            $table->string('latin', 50);

            // Panduan pengucapan
            $table->string('pronunciation', 100)->nullable();

            // Kategori: aksara_dasar, pasangan, sandhangan, angka, dll.
            $table->string('category', 50)->index();

            // Urutan tampil dalam kategori
            $table->unsignedSmallInteger('sort_order')->default(0);

            // XP yang didapat saat mempelajari karakter ini
            $table->unsignedSmallInteger('xp_reward')->default(10);

            // Media pendukung
            $table->string('image_url')->nullable();
            $table->string('audio_url')->nullable();

            // Catatan / penjelasan tambahan
            $table->text('notes')->nullable();

            $table->timestamps();

            // Satu karakter unik per jenis aksara
            $table->unique(['script_type', 'character'], 'unique_script_character');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
