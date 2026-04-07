<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_profile_reminder_at')->nullable()->after('remember_token');
            $table->timestamp('last_payment_reminder_at')->nullable()->after('last_profile_reminder_at');
            $table->timestamp('last_social_reminder_at')->nullable()->after('last_payment_reminder_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_profile_reminder_at', 'last_payment_reminder_at', 'last_social_reminder_at']);
        });
    }
};
?>
