<?php

use App\Enums\UserRole;
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
        // 1. Buat enum type di PostgreSQL
        DB::statement("CREATE TYPE user_role AS ENUM ('admin', 'member', 'guest', 'user', 'developer')");

        // 2. Buat tabel dengan kolom bertipe enum PostgreSQL
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', UserRole::cases())->after('name')
                ->default(UserRole::User);
        });

        // 3. Ubah kolom ke enum PostgreSQL secara manual
        DB::statement("ALTER TABLE users ALTER COLUMN role TYPE user_role USING role::user_role");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 4. Hapus enum type di PostgreSQL
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        DB::statement("DROP TYPE user_role");
    }
};
