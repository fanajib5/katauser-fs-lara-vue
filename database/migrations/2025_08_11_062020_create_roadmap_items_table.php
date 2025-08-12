<?php

use App\Enums\RoadmapItemStatus;
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
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("CREATE TYPE roadmap_item_status AS ENUM ('draft', 'planned', 'archived', 'completed', 'in_progress', 'cancelled')");
        }

        Schema::create('roadmap_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id');
            $table->foreignId('organization_id')->constrained('organizations')->restrictOnDelete();
            $table->foreignId('feedback_post_id')->nullable()->constrained('feedback_posts')->restrictOnDelete();
            $table->string('title');
            $table->text('content');
            $table->enum('status', RoadmapItemStatus::cases());
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestampsTz();
            $table->softDeletesTz();
        });

        // Only run ENUM type creation for PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE roadmap_items ALTER COLUMN status TYPE roadmap_item_status USING status::roadmap_item_status");
            DB::statement("ALTER TABLE roadmap_items ALTER COLUMN status SET DEFAULT 'draft'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roadmap_items');

        // Only run ENUM type creation for PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("DROP TYPE IF EXISTS roadmap_item_status");
        }
    }
};
