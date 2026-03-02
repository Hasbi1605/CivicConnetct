<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policy_briefs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lab_room_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('summary');
            $table->text('problem'); // Ringkasan Masalah
            $table->text('analysis'); // Analisis & Data
            $table->text('recommendation'); // Rekomendasi Kebijakan
            $table->string('template_type')->default('standar'); // standar, data-driven, quick-response
            $table->string('status')->default('draft'); // draft, pending, approved, rejected
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('policy_endorsements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_brief_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unique(['policy_brief_id', 'user_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policy_endorsements');
        Schema::dropIfExists('policy_briefs');
    }
};
