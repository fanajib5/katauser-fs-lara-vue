<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::raw('CREATE INDEX IF NOT EXISTS feedback_analyses_keywords_gin ON public.feedback_analyses USING gin (keywords)');
        DB::raw('CREATE INDEX IF NOT EXISTS feedback_posts_metadata_gin ON public.feedback_posts USING gin (metadata)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::raw('DROP INDEX IF EXISTS feedback_analyses_keywords_gin');
        DB::raw('DROP INDEX IF EXISTS feedback_posts_metadata_gin');
    }
};
