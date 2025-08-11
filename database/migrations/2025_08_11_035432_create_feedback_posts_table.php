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
        DB::statement("CREATE TYPE feedback_post_source_url AS ENUM ('embed', 'public_page')");
        DB::statement("CREATE TYPE feedback_post_status AS ENUM ('open', 'planned', 'completed', 'in_progress', 'archived', 'closed')");
        DB::statement("CREATE TYPE feedback_post_type AS ENUM ('feature', 'bug', 'improvement', 'question', 'suggestion', 'other')");

        Schema::create('feedback_posts', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id');
            $table->foreignId('feedback_board_id')->constrained('feedback_boards')->restrictOnDelete();
            $table->foreignId('member_id')->constrained('members')->restrictOnDelete();
            $table->string('title');
            $table->text('description');
            $table->enum('source', FeedbackPostSource::cases());
            $table->string('source_url')->nullable();
            $table->enum('status', FeedbackPostStatus::cases());
            $table->enum('type', FeedbackPostType::cases());
            $table->jsonb('metadata')->nullable();
            $table->timestampTz('set_to_public_at')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_posts');
    }
};
