<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agency: Company profile; hasMany Creators; commission % applied in transactions.
     */
    public function up(): void
    {
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('gst_number')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode', 20)->nullable();
            $table->string('website')->nullable();
            $table->text('kyc_documents')->nullable(); // JSON paths or JSON array of document paths
            $table->string('approval_status')->default('pending'); // pending, approved, rejected
            $table->decimal('commission_percent', 5, 2)->default(0);
            $table->timestamps();
        });

        Schema::table('agencies', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('approval_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agencies');
    }
};
