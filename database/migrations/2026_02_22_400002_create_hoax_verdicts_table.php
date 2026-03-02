<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hoax_verdicts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoax_claim_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('verdict'); // valid/misleading/hoax
            $table->string('evidence_url')->nullable();
            $table->text('reasoning')->nullable();
            $table->string('status')->default('pending'); // pending/approved/rejected
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->unique(['hoax_claim_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hoax_verdicts');
    }
};
