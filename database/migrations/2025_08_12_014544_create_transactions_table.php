<?php

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
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
        DB::statement("CREATE TYPE transaction_status AS ENUM ('pending', 'paid', 'failed', 'cancelled', 'refunded')");

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code');
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('plan_id')->nullable()->comment('hanya untuk subscription')
                ->constrained('plans')->restrictOnDelete();
            $table->string('payment_method');
            $table->jsonb('custom_package_details');
            $table->decimal('total_amount', 12, 2);
            $table->string('currency')->default('IDR');
            $table->decimal('credit_amount', 12, 2)->default(0)->comment('hanya untuk top-up');
            $table->enum('status', TransactionStatus::cases());
            $table->jsonb('metadata')->comment('dari payment gateway');
            $table->timestampTz('paid_at')->nullable();
            $table->timestampTz('expires_at')->nullable();
            $table->timestampsTz();
        });

        DB::statement("ALTER TABLE transactions ALTER COLUMN status TYPE transaction_status USING status::transaction_status");
        DB::statement("ALTER TABLE transactions ALTER COLUMN status SET DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');

        DB::statement('DROP TYPE IF EXISTS transaction_status');
    }
};
