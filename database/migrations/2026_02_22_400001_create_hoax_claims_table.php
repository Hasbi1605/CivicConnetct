<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hoax_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title'); // the claim text
            $table->text('description')->nullable(); // context / why it's suspicious
            $table->string('source_url')->nullable();
            $table->string('source_platform')->default('lainnya'); // twitter/whatsapp/facebook/website/lainnya
            $table->string('category')->default('lainnya'); // politik/kesehatan/teknologi/sosial/lainnya
            $table->string('status')->default('pending'); // pending/open/resolved
            $table->string('final_verdict')->nullable(); // valid/misleading/hoax
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hoax_claims');
    }
};
