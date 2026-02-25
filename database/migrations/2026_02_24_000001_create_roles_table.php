<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * RBAC: Roles for Admin, Brand, Creator, Agency, StudioOwner, Customer.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Display name
            $table->string('slug')->unique(); // admin, brand, creator, agency, studio_owner, customer
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
