<?php

use App\Enums\PlanType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run ENUM type creation for PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("CREATE TYPE plan_type AS ENUM ('subscription', 'payg', 'custom')");
        }

        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', PlanType::cases());
            $table->decimal('price', 10, 2);
            $table->string('duration_days')->nullable();
            $table->decimal('included_credits', 10, 2)->nullable();
            $table->boolean('is_active')->default(false);
            $table->jsonb('features');
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestampsTz();
            $table->softDeletesTz();
        });

        // Only run column type alteration for PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE plans ALTER COLUMN type TYPE plan_type USING type::plan_type");
            DB::statement("ALTER TABLE plans ALTER COLUMN type SET DEFAULT 'subscription'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP TYPE IF EXISTS plan_type');
        }
    }
};
