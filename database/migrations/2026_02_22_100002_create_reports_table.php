<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->string('reason'); // hoaks, spam, ujaran-kebencian, lainnya
            $table->text('description')->nullable();
            $table->string('status')->default('pending'); // pending, reviewed, dismissed
            $table->timestamps();

            $table->unique(['user_id', 'post_id']); // 1 user 1 report per post
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
