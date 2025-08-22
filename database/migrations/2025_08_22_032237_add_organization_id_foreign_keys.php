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
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()
                ->after('id')->constrained('organizations')->cascadeOnDelete();
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()
                ->after('id')->constrained('organizations')->cascadeOnDelete();
        });

        Schema::table('user_credits', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()
                ->after('id')->constrained('organizations')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
        });

        Schema::table('user_credits', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn('organization_id');
        });
    }
};
