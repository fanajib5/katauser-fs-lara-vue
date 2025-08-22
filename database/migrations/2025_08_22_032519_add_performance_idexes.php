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
        Schema::table('surveys', function (Blueprint $table) {
            $table->index('organization_id');
        });
        Schema::table('survey_types', function (Blueprint $table) {
            $table->index('organization_id');
        });
        Schema::table('feedback_boards', function (Blueprint $table) {
            $table->index('organization_id');
        });
        Schema::table('roadmaps', function (Blueprint $table) {
            $table->index('organization_id');
        });
        Schema::table('roadmap_items', function (Blueprint $table) {
            $table->index('organization_id');
        });
        Schema::table('changelogs', function (Blueprint $table) {
            $table->index('organization_id');
        });
        Schema::table('tags', function (Blueprint $table) {
            $table->index('organization_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->index('organization_id');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->index('feedback_post_id');
        });
        Schema::table('votes', function (Blueprint $table) {
            $table->index('feedback_post_id');
        });
        Schema::table('feedback_analyses', function (Blueprint $table) {
            $table->index('feedback_post_id');
        });

        Schema::table('survey_questions', function (Blueprint $table) {
            $table->index('survey_id');
        });
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->index('survey_id');
        });
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->index('member_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['organization_id']);
        });
        Schema::table('tags', function (Blueprint $table) {
            $table->dropIndex(['organization_id']);
        });
        Schema::table('feedback_boards', function (Blueprint $table) {
            $table->dropIndex(['organization_id']);
        });
        Schema::table('roadmaps', function (Blueprint $table) {
            $table->dropIndex(['organization_id']);
        });
        Schema::table('roadmap_items', function (Blueprint $table) {
            $table->dropIndex(['organization_id']);
        });
        Schema::table('changelogs', function (Blueprint $table) {
            $table->dropIndex(['organization_id']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['feedback_post_id']);
        });
        Schema::table('votes', function (Blueprint $table) {
            $table->dropIndex(['feedback_post_id']);
        });
        Schema::table('feedback_analyses', function (Blueprint $table) {
            $table->dropIndex(['feedback_post_id']);
        });

        Schema::table('survey_questions', function (Blueprint $table) {
            $table->dropIndex(['survey_id']);
        });
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->dropIndex(['survey_id']);
        });
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->dropIndex(['member_id']);
        });
    }
};
