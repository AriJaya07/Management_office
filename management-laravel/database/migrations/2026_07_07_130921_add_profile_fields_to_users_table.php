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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('password')->constrained()->nullOnDelete();
            $table->foreignId('position_id')->nullable()->after('department_id')->constrained()->nullOnDelete();
            $table->string('phone')->nullable()->after('position_id');
            $table->date('join_date')->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('join_date');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
            $table->dropConstrainedForeignId('position_id');
            $table->dropColumn(['phone', 'join_date', 'is_active']);
            $table->dropSoftDeletes();
        });
    }
};
