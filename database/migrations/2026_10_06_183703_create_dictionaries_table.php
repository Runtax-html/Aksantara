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
        Schema::create('dictionaries', function (Blueprint $table) {
            $table->id();

            // Jenis aksara: jawa, sunda, bali, dll.
            $table->string('script_type', 50)->index();

            // Kata dalam aksara (Unicode)
            $table->string('word_aksara', 255);

            // Transliterasi Latin
            $table->string('word_latin', 255)->index();

            // Arti dalam Bahasa Indonesia
            $table->text('meaning');

            // Kategori kata: kata benda, kata kerja, dll.
            $table->string('word_category', 50)->nullable();

            // Contoh penggunaan dalam kalimat (aksara)
            $table->text('example_aksara')->nullable();

            // Contoh penggunaan dalam kalimat (Latin)
            $table->text('example_latin')->nullable();

            // Media pendukung
            $table->string('audio_url')->nullable();
            $table->string('image_url')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dictionaries');
    }
};
