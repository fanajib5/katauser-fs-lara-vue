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
        Schema::create('feedback_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feedback_post_id')->constrained()->cascadeOnDelete();
            $table->float('sentiment_score')->nullable(); // -1..1
            $table->string('sentiment_label',20)->nullable(); // positive/neutral/negative
            $table->jsonb('keywords')->nullable();          // ["billing","bug","ui"]
            $table->jsonb('extra')->default(DB::raw("'{}'::jsonb")); // pipeline metadata
            $table->timestampsTz();
            $table->index(['sentiment_label']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_analyses');
    }
};
