<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use App\Models\Organization;

class UserWithPermissionSeeder extends Seeder
{
    private $permissions = [
        // Users & Organizations
        'viewAny users',
        'view users',
        'create users',
        'update users',
        'delete users',
        'restore users',
        'forceDelete users',

        'viewAny organizations',
        'view organizations',
        'create organizations',
        'update organizations',
        'delete organizations',
        'restore organizations',
        'forceDelete organizations',

        'viewAny members',
        'view members',
        'create members',
        'update members',
        'delete members',
        'restore members',
        'forceDelete members',

        // Surveys & Questions
        'viewAny surveys',
        'view surveys',
        'create surveys',
        'update surveys',
        'delete surveys',
        'restore surveys',
        'forceDelete surveys',

        'viewAny questions',
        'view questions',
        'create questions',
        'update questions',
        'delete questions',
        'restore questions',
        'forceDelete questions',

        // Feedback & Analysis
        'viewAny feedback_posts', // Mengacu pada tabel feedback_posts di dump
        'view feedback_posts',
        'create feedback_posts',
        'update feedback_posts',
        'delete feedback_posts',
        'restore feedback_posts',
        'forceDelete feedback_posts',

        'viewAny feedback_boards', // Mengacu pada tabel feedback_boards di dump
        'view feedback_boards',
        'create feedback_boards',
        'update feedback_boards',
        'delete feedback_boards',
        'restore feedback_boards',
        'forceDelete feedback_boards',

        'viewAny feedback_analysis', // Mengacu pada entitas Feedback Analysis di dokumen
        'view feedback_analysis',
        'create feedback_analysis', // Misalnya untuk trigger manual analysis
        // Update/Delete mungkin tidak relevan untuk hasil analisis

        // Tags
        'viewAny tags',
        'view tags',
        'create tags',
        'update tags',
        'delete tags',
        'restore tags',
        'forceDelete tags',

        // Analytics & Reports
        'viewAny analytics_reports', // Mengacu pada entitas Analytics Report di dokumen
        'view analytics_reports',
        'export analytics_reports',

        // Comments (dari dump)
        'viewAny comments',
        'view comments',
        'create comments',
        'update comments',
        'delete comments',
        'restore comments',
        'forceDelete comments',

        // Roadmap Items (dari dump)
        'viewAny roadmap_items',
        'view roadmap_items',
        'create roadmap_items',
        'update roadmap_items',
        'delete roadmap_items',
        'restore roadmap_items',
        'forceDelete roadmap_items',

        // Changelogs (dari dump)
        'viewAny changelogs',
        'view changelogs',
        'create changelogs',
        'update changelogs',
        'delete changelogs',
        'restore changelogs',
        'forceDelete changelogs',

        // Plans, Subscriptions, Transactions (dari dump)
        'viewAny plans',
        'view plans',
        'create plans',
        'update plans',
        'delete plans',

        'viewAny subscriptions',
        'view subscriptions',
        'create subscriptions', // Mungkin untuk admin
        'update subscriptions',
        'delete subscriptions',

        'viewAny transactions',
        'view transactions',

        'viewAny user_credits',
        'view user_credits',

        // Items (dari dump - untuk paket/produk)
        'viewAny items',
        'view items',
        'create items',
        'update items',
        'delete items',

        // Audit Trails (dari dump)
        'viewAny audit_trails',
        'view audit_trails',

        // Roles & Permissions (Admin)
        'viewAny roles',
        'view roles',
        'create roles',
        'update roles',
        'delete roles',

        'viewAny permissions',
        'view permissions',
        'create permissions',
        'update permissions',
        'delete permissions',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ----------------------------
        // 1. CREATE PERMISSIONS
        // ----------------------------
        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ----------------------------
        // 2. CREATE ROLES & ASSIGN PERMISSIONS
        // ----------------------------

        // Role: Super Admin / Developer (akses penuh ke sistem, termasuk manajemen global)
        //       diletakkan pada /AppServiceProvider.php melalui fitur Gate
        $developerRole = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']); // role that has full access rights on the platform
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']); // role that has full access rights on the organization
        $memberRole = Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']); // role that is registered within the organization
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']); // role that does not belong to any organization

        // Assign permissions to roles

        // Member: Hanya bisa melihat dan membuat feedback terkait
        $memberPermissions = [
            'viewAny feedback_posts',
            'view feedback_posts',
            'create feedback_posts',
            'viewAny feedback_boards',
            'view feedback_boards',
            'viewAny comments',
            'view comments',
            'create comments',
            'update comments', // Hanya komentar mereka sendiri, ditangani oleh policy
            'delete comments', // Hanya komentar mereka sendiri, ditangani oleh policy
            'viewAny roadmap_items',
            'view roadmap_items',
            'viewAny changelogs',
            'view changelogs',
            'viewAny analytics_reports', // Mungkin bisa melihat laporan organisasi mereka
            'view analytics_reports',
        ];
        $memberRole->syncPermissions(array_intersect($this->permissions, $memberPermissions));

        // Owner: Bisa mengelola organisasi mereka dan segala sesuatu di dalamnya
        $adminPermissions = array_merge($memberPermissions, [
            // Organization Management
            'viewAny organizations',
            'view organizations',
            'update organizations', // Hanya organisasi mereka
            'viewAny members',
            'view members',
            'create members',
            'update members', // Hanya anggota organisasi mereka, ditangani policy
            'delete members',

            // Survey Management
            'viewAny surveys',
            'view surveys',
            'create surveys',
            'update surveys', // Hanya survey organisasi mereka
            'delete surveys',
            'restore surveys',
            'viewAny questions',
            'view questions',
            'create questions',
            'update questions',
            'delete questions',

            // Feedback Management
            'update feedback_posts', // Untuk merespons atau mengubah status
            'delete feedback_posts', // Mungkin bisa menghapus
            'restore feedback_posts',
            'create feedback_boards',
            'update feedback_boards',
            'delete feedback_boards',
            'view feedback_analysis', // Melihat hasil analisis feedback mereka
            'viewAny tags',
            'view tags',
            'create tags',
            'update tags',
            'delete tags',

            // Roadmap & Changelog
            'viewAny roadmap_items',
            'view roadmap_items',
            'create roadmap_items',
            'update roadmap_items',
            'delete roadmap_items',
            'viewAny changelogs',
            'view changelogs',
            'create changelogs',
            'update changelogs',
            'delete changelogs',

            // Analytics
            'viewAny analytics_reports',
            'view analytics_reports',
            'export analytics_reports',

            // Subscription related (view only)
            'viewAny subscriptions',
            'view subscriptions',
            'viewAny transactions',
            'view transactions',
            'viewAny user_credits',
            'view user_credits',
            'viewAny plans',
            'view plans',
            'viewAny items',
            'view items',
        ]);
        $adminRole->syncPermissions(array_intersect($this->permissions, $adminPermissions));

        // Admin (Platform): Bisa mengelola keseluruhan platform
        $adminPermissions = array_merge($adminPermissions, [
            // User Management
            'viewAny users',
            'view users',
            'create users',
            'update users',
            'delete users',
            'restore users',

            // Organization Management (All)
            'create organizations',
            'delete organizations',
            'restore organizations',
            'forceDelete organizations',

            // Plan, Item, Subscription Management
            'create plans',
            'update plans',
            'delete plans',
            'create items',
            'update items',
            'delete items',

            // Audit Trails
            'viewAny audit_trails',
            'view audit_trails',

            // Role & Permission Management (Opsional, tergantung kebutuhan)
            // 'viewAny roles',
            // 'view roles',
            // 'create roles',
            // 'update roles',
            // 'delete roles',
            // 'viewAny permissions',
            // 'view permissions',
            // 'create permissions',
            // 'update permissions',
            // 'delete permissions',
        ]);
        $adminRole->syncPermissions(array_intersect($this->permissions, $adminPermissions));

        // ----------------------------
        // 3. CREATE ORGANIZATION & USERS
        // ----------------------------

        // Create demo users and assign roles
        // Note: Kita buat organisasi dulu jika user perlu dihubungkan

        $defaultPassword = bcrypt('pass1234');
        $now = now();

        // 1. Developer User
        $developerUser = User::factory()->create([
            'name' => 'Developer',
            'email' => 'admin@katauser.com',
            'password' => '$2a$12$YEOYclRDJRYLiqTosE35Duc5qs/euAG01gJz1dmoLpm1IpTkyr4q6',
            'email_verified_at' => $now,
            'created_at' => '1997-05-07 19:00:00',
            'updated_at' => $now,
        ]);
        $developerUser->assignRole($developerRole);
        // Developer biasanya tidak perlu organisasi

        // 2. Platform Admin User
        $adminUser = User::factory()->create([
            'name' => 'Platform Admin',
            'email' => 'admin@example.com',
            'password' => $defaultPassword,
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $adminUser->assignRole($adminRole);

        // 3. Organization Owner User
        // Buat organisasi dulu
        $organization = Organization::factory()->create([
            'name' => 'KataUser Demo Org',
            'slug' => 'katauser-demo',
            'subdomain' => 'demo',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $adminUser = User::factory()->create([
            'name' => 'Pemilik Organisasi',
            'email' => 'owner@katauser.com',
            'password' => $defaultPassword,
            'organization_id' => $organization->id, // Hubungkan ke organisasi
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $adminUser->assignRole($adminRole);

        // 4. Member User (dalam organisasi)
        $memberUser = User::factory()->create([
            'name' => 'Anggota Organisasi',
            'email' => 'member@katauser.com',
            'password' => $defaultPassword,
            'organization_id' => $organization->id, // Hubungkan ke organisasi
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $memberUser->assignRole($memberRole);

        // 5. Regular User (belum tergabung organisasi)
        $regularUser = User::factory()->create([
            'name' => 'Pengguna Biasa',
            'email' => 'user@example.com',
            'password' => $defaultPassword,
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $regularUser->assignRole($memberRole); // Default role member

        // ----------------------------
        // 4. BUAT RELASI MEMBER (User <-> Organization)
        // ----------------------------

        $now = now();
        $creatorId = $developerUser->id;

        // Cek apakah member sudah ada
        DB::table('members')->upsert([
            [
                'public_id' => (string) \Str::uuid(),
                'user_id' => $adminUser->id,
                'organization_id' => $organization->id,
                'created_by' => $creatorId,
                'updated_by' => $creatorId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'public_id' => (string) \Str::uuid(),
                'user_id' => $memberUser->id,
                'organization_id' => $organization->id,
                'created_by' => $creatorId,
                'updated_by' => $creatorId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['user_id', 'organization_id'], ['updated_at', 'updated_by']);
    }
}
