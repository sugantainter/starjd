<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove legacy users.role column; role is determined via user_roles pivot (RBAC).
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function ($table) {
                $table->dropColumn('role');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function ($table) {
                $table->string('role')->default('customer')->after('email');
            });
        }
    }
};
