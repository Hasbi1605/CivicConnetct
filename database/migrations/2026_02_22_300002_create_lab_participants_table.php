<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('joined_at')->useCurrent();
            $table->unique(['lab_room_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_participants');
    }
};
