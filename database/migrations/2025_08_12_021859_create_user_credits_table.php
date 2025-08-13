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
        Schema::create('user_credits', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id');
            $table->foreignId('user_id')->constrained('users')->cascadecascadeOnDelete();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadecascadeOnDelete();
            $table->integer('change_amount')->comment('positif untuk penambahan, negatif untuk pengurangan');
            $table->integer('balance_after')->comment('sisa saldo credit setelah perubahan');
            $table->text('description')->nullable();
            $table->timestampTz('created_at')->default(now());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_credits');
    }
};
