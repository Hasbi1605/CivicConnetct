<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds institutional identity verification fields (KYA — Know Your Academician).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('identity_card_type', ['ktm', 'ktd'])->nullable()->after('is_profile_complete');
            $table->string('identity_card_image')->nullable()->after('identity_card_type');
            $table->string('nim_nidn')->nullable()->after('identity_card_image');
            $table->enum('identity_status', ['unsubmitted', 'pending', 'approved', 'rejected'])
                ->default('unsubmitted')
                ->after('nim_nidn');
            $table->text('identity_rejection_reason')->nullable()->after('identity_status');
            $table->timestamp('identity_verified_at')->nullable()->after('identity_rejection_reason');
            $table->foreignId('identity_verified_by')->nullable()->after('identity_verified_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['identity_verified_by']);
            $table->dropColumn([
                'identity_card_type',
                'identity_card_image',
                'nim_nidn',
                'identity_status',
                'identity_rejection_reason',
                'identity_verified_at',
                'identity_verified_by',
            ]);
        });
    }
};
