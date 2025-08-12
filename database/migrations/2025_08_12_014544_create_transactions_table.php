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
        DB::statement("CREATE TYPE transaction_type AS ENUM ('subscription', 'topup', 'custom')");
        DB::statement("CREATE TYPE transaction_status AS ENUM ('pending', 'paid', 'failed', 'cancelled', 'refunded')");

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id');
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->enum('type', TransactionType::cases());
            $table->foreignId('plan_id')->nullable()->comment('hanya untuk subscription')
                ->constrained('plans')->restrictOnDelete();
            $table->jsonb('custom_package_details');
            $table->decimal('amount', 12, 2);
            $table->decimal('credit_amount', 12, 2)->default(0)->comment('hanya untuk top-up');
            $table->enum('status', TransactionStatus::cases());
            $table->string('payment_reference')->comment('dari payment gateway');
            $table->jsonb('payment_details')->comment('dari payment gateway');
            $table->timestampTz('paid_at')->nullable();
            $table->timestampsTz();
        });

        DB::statement("ALTER TABLE transactions ALTER COLUMN type TYPE transaction_type USING type::transaction_type");
        DB::statement("ALTER TABLE transactions ALTER COLUMN type SET DEFAULT 'subscription'");
        DB::statement("ALTER TABLE transactions ALTER COLUMN status TYPE transaction_status USING status::transaction_status");
        DB::statement("ALTER TABLE transactions ALTER COLUMN status SET DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');

        DB::statement('DROP TYPE IF EXISTS transaction_type');
        DB::statement('DROP TYPE IF EXISTS transaction_status');
    }
};
