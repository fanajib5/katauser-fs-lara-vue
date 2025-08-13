<?php

use App\Enums\FeedbackPostSource;
use App\Enums\FeedbackPostStatus;
use App\Enums\FeedbackPostType;
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
            DB::statement("CREATE TYPE feedback_post_source AS ENUM ('embed', 'public_page')");
            DB::statement("CREATE TYPE feedback_post_status AS ENUM ('open', 'planned', 'completed', 'in_progress', 'archived', 'closed')");
            DB::statement("CREATE TYPE feedback_post_type AS ENUM ('feature', 'bug', 'improvement', 'question', 'suggestion', 'other')");
        }

        Schema::create('feedback_posts', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id');
            $table->foreignId('feedback_board_id')->constrained('feedback_boards')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->enum('source', FeedbackPostSource::cases());
            $table->string('source_url')->nullable();
            $table->enum('status', FeedbackPostStatus::cases());
            $table->enum('type', FeedbackPostType::cases());
            $table->jsonb('metadata')->nullable();
            $table->timestampTz('set_to_public_at')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestampsTz();
            $table->softDeletesTz();
        });

        // Only run ENUM type creation for PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE feedback_posts ALTER COLUMN source TYPE feedback_post_source USING source::feedback_post_source");
            DB::statement("ALTER TABLE feedback_posts ALTER COLUMN status TYPE feedback_post_status USING status::feedback_post_status");
            DB::statement("ALTER TABLE feedback_posts ALTER COLUMN type TYPE feedback_post_type USING type::feedback_post_type");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_posts');

        // Only run ENUM type creation for PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("DROP TYPE IF EXISTS feedback_post_source");
            DB::statement("DROP TYPE IF EXISTS feedback_post_status");
            DB::statement("DROP TYPE IF EXISTS feedback_post_type");
        }
    }
};
