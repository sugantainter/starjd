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
        Schema::table('social_accounts', function (Blueprint $table) {
            $table->text('refresh_token')->nullable()->after('access_token');
            $table->timestamp('expires_at')->nullable()->after('refresh_token');
            $table->text('token_secret')->nullable()->after('expires_at'); // for potentially adding Twitter/X later
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_accounts', function (Blueprint $table) {
            $table->dropColumn(['refresh_token', 'expires_at', 'token_secret']);
        });
    }
};
