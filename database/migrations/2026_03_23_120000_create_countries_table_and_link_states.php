<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('code', 10)->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('countries') && DB::table('countries')->count() === 0) {
            DB::table('countries')->insert([
                'name' => 'India',
                'slug' => 'india',
                'code' => 'IN',
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (! Schema::hasColumn('states', 'country_id')) {
            $defaultCountryId = DB::table('countries')->orderBy('id')->value('id');
            $hasRows = DB::table('states')->exists();

            Schema::table('states', function (Blueprint $table) use ($hasRows) {
                if ($hasRows) {
                    $table->foreignId('country_id')->nullable()->after('id')->constrained('countries')->cascadeOnDelete();
                } else {
                    $table->foreignId('country_id')->after('id')->constrained('countries')->cascadeOnDelete();
                }
            });

            if ($hasRows && $defaultCountryId) {
                DB::table('states')->whereNull('country_id')->update(['country_id' => $defaultCountryId]);
            }

            if ($hasRows && Schema::getConnection()->getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE states MODIFY country_id BIGINT UNSIGNED NOT NULL');
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('states', 'country_id')) {
            Schema::table('states', function (Blueprint $table) {
                $table->dropForeign(['country_id']);
            });
            Schema::table('states', function (Blueprint $table) {
                $table->dropColumn('country_id');
            });
        }

        // Do not drop `countries` here: it may have been created outside this migration.
    }
};
