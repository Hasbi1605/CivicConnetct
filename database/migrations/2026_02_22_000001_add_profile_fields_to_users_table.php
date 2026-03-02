<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('jurusan')->nullable()->after('name');
            $table->string('universitas')->nullable()->after('jurusan');
            $table->text('bio')->nullable()->after('universitas');
            $table->string('role')->default('mahasiswa')->after('bio'); // mahasiswa, mentor, agent
            $table->string('avatar')->nullable()->after('role');
            $table->boolean('is_profile_complete')->default(false)->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['jurusan', 'universitas', 'bio', 'role', 'avatar', 'is_profile_complete']);
        });
    }
};
