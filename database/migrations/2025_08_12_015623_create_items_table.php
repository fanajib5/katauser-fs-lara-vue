<?php

use App\Enums\ItemType;
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
            DB::statement("CREATE TYPE item_type AS ENUM ('plan', 'credit', 'custom_package')");
        }

        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->nullable()->constrained('plans');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->enum('type', ItemType::cases());
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestampsTz();
            $table->softDeletesTz();
        });

        // Only run ENUM type creation for PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE items ALTER COLUMN type TYPE item_type USING type::item_type");
            DB::statement("ALTER TABLE items ALTER COLUMN type SET DEFAULT 'plan'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');

        // Only run ENUM type creation for PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP TYPE IF EXISTS item_type');
        }
    }
};
