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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('plan_id')->constrained();
            $table->string('name');
            $table->string('slug')->unique();        // free -> /{slug}
            $table->string('subdomain')->unique();   // pro -> {subdomain}.katauser.com
            $table->string('custom_domain')->unique()->nullable(); // enterprise
            $table->timestampTz('domain_verified_at')->nullable();
            $table->jsonb('urls')->default(DB::raw("'{}'::jsonb"));
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
