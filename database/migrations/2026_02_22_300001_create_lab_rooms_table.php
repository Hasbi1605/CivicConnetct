<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category'); // fact-check, kebijakan, sosial, lainnya
            $table->string('phase')->default('literasi'); // literasi, analisis, output
            $table->string('status')->default('open'); // open, in_progress, completed
            $table->boolean('is_private')->default(false);
            $table->integer('max_participants')->default(6);
            $table->string('target')->nullable(); // Policy Brief, Fact-Check, Video Edukasi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_rooms');
    }
};
