<?php

use App\Enums\VoteType;
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
            DB::statement("CREATE TYPE vote_type AS ENUM ('upvote', 'downvote')");
        }

        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feedback_post_id')->constrained('feedback_posts')->restrictOnDelete();
            $table->foreignId('member_id')->constrained('members')->restrictOnDelete();
            $table->enum('type', VoteType::cases());
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index(['feedback_post_id', 'member_id'], 'votes_feedback_post_id_member_id_index');
        });

        // Only run ENUM type creation for PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE votes ALTER COLUMN type TYPE vote_type USING type::vote_type");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');

        // Only run ENUM type creation for PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("DROP TYPE IF EXISTS vote_type");
        }
    }
};
