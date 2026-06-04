<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $sysAdmin    = User::where('email', 'sysadmin@business.test')->first();
        $adminRole   = Role::where('slug', Role::ADMIN)->first();
        $moderatorRole = Role::where('slug', Role::MODERATOR)->first();

        // ── Administratori ─────────────────────────────────────────────────
        $adminData = [
            [
                'name'  => 'Milena Đorđević',
                'email' => 'milena.djordjevic@business.test',
            ],
            [
                'name'  => 'Dragan Vasić',
                'email' => 'dragan.vasic@business.test',
            ],
            [
                'name'  => 'Jelena Filipović',
                'email' => 'jelena.filipovic@business.test',
            ],
        ];

        $admins = [];
        foreach ($adminData as $data) {
            $admins[] = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => Hash::make('123'),
                'role_id'           => $adminRole->id,
                'is_active'         => true,
                'created_by'        => $sysAdmin->id,
                'email_verified_at' => now(),
            ]);
        }

        // ── Moderatori ─────────────────────────────────────────────────────
        // Realističan srpski tim — prodaja, marketing, podrška, računovodstvo
        $moderatorData = [
            // Prodajni tim (dodao admin Milena)
            ['name' => 'Aleksandar Petrović',  'email' => 'aleksandar.petrovic@business.test',  'active' => true,  'creator' => $admins[0]],
            ['name' => 'Ivana Stojanović',      'email' => 'ivana.stojanovic@business.test',      'active' => true,  'creator' => $admins[0]],
            ['name' => 'Bojan Ristić',          'email' => 'bojan.ristic@business.test',          'active' => true,  'creator' => $admins[0]],
            ['name' => 'Tamara Jovanović',      'email' => 'tamara.jovanovic@business.test',      'active' => false, 'creator' => $admins[0]],

            // Marketing tim (dodao admin Dragan)
            ['name' => 'Nemanja Lukić',         'email' => 'nemanja.lukic@business.test',         'active' => true,  'creator' => $admins[1]],
            ['name' => 'Maja Nikolić',          'email' => 'maja.nikolic@business.test',           'active' => true,  'creator' => $admins[1]],
            ['name' => 'Srdjan Marković',       'email' => 'srdjan.markovic@business.test',       'active' => false, 'creator' => $admins[1]],

            // Podrška i računovodstvo (dodala admin Jelena)
            ['name' => 'Katarina Pavlović',     'email' => 'katarina.pavlovic@business.test',     'active' => true,  'creator' => $admins[2]],
            ['name' => 'Vladimir Ilić',         'email' => 'vladimir.ilic@business.test',         'active' => true,  'creator' => $admins[2]],
            ['name' => 'Ana Simić',             'email' => 'ana.simic@business.test',             'active' => true,  'creator' => $admins[2]],
            ['name' => 'Miloš Đokić',           'email' => 'milos.djokic@business.test',          'active' => false, 'creator' => $admins[2]],

            // Dodao direktno sysadmin
            ['name' => 'Zorana Todorović',      'email' => 'zorana.todorovic@business.test',      'active' => true,  'creator' => $sysAdmin],
            ['name' => 'Stefan Kovačević',      'email' => 'stefan.kovacevic@business.test',      'active' => true,  'creator' => $sysAdmin],
        ];

        $moderators = [];
        foreach ($moderatorData as $data) {
            $moderators[] = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => Hash::make('123'),
                'role_id'           => $moderatorRole->id,
                'is_active'         => $data['active'],
                'created_by'        => $data['creator']->id,
                'email_verified_at' => now(),
            ]);
        }

        // ── Dodatni activity logs (simulacija realnog korišćenja) ──────────
        $this->seedActivityLogs($sysAdmin, $admins, $moderators);
    }

    private function seedActivityLogs(User $sysAdmin, array $admins, array $moderators): void
    {
        $now = now();

        $scenarios = [
            [
                'user_id'    => $sysAdmin->id,
                'model_type' => User::class,
                'model_id'   => $admins[0]->id,
                'action'     => ActivityLog::ACTION_UPDATED,
                'old_values' => ['is_active' => false],
                'new_values' => ['is_active' => true],
                'changes'    => ['is_active' => true],
                'ip_address' => '10.0.1.1',
                'created_at' => $now->copy()->subDays(12),
            ],
            [
                'user_id'    => $admins[0]->id,
                'model_type' => User::class,
                'model_id'   => $moderators[3]->id,
                'action'     => ActivityLog::ACTION_UPDATED,
                'old_values' => ['is_active' => true],
                'new_values' => ['is_active' => false],
                'changes'    => ['is_active' => false],
                'ip_address' => '10.0.1.15',
                'created_at' => $now->copy()->subDays(7),
            ],
            [
                'user_id'    => $admins[1]->id,
                'model_type' => User::class,
                'model_id'   => $moderators[6]->id,
                'action'     => ActivityLog::ACTION_UPDATED,
                'old_values' => ['is_active' => true],
                'new_values' => ['is_active' => false],
                'changes'    => ['is_active' => false],
                'ip_address' => '10.0.1.22',
                'created_at' => $now->copy()->subDays(5),
            ],
            [
                'user_id'    => $sysAdmin->id,
                'model_type' => User::class,
                'model_id'   => $admins[2]->id,
                'action'     => ActivityLog::ACTION_VIEWED,
                'old_values' => null,
                'new_values' => null,
                'changes'    => null,
                'ip_address' => '10.0.1.1',
                'created_at' => $now->copy()->subDays(3),
            ],
            [
                'user_id'    => $admins[2]->id,
                'model_type' => User::class,
                'model_id'   => $moderators[10]->id,
                'action'     => ActivityLog::ACTION_UPDATED,
                'old_values' => ['is_active' => true],
                'new_values' => ['is_active' => false],
                'changes'    => ['is_active' => false],
                'ip_address' => '10.0.1.30',
                'created_at' => $now->copy()->subDays(2),
            ],
            [
                'user_id'    => $sysAdmin->id,
                'model_type' => User::class,
                'model_id'   => $admins[0]->id,
                'action'     => ActivityLog::ACTION_UPDATED,
                'old_values' => ['name' => 'Milena Đorđević'],
                'new_values' => ['name' => 'Milena Đorđević'],
                'changes'    => ['name' => 'Milena Đorđević'],
                'ip_address' => '10.0.1.1',
                'created_at' => $now->copy()->subHours(8),
            ],
            [
                'user_id'    => $admins[0]->id,
                'model_type' => User::class,
                'model_id'   => $moderators[1]->id,
                'action'     => ActivityLog::ACTION_VIEWED,
                'old_values' => null,
                'new_values' => null,
                'changes'    => null,
                'ip_address' => '10.0.1.15',
                'created_at' => $now->copy()->subHours(2),
            ],
        ];

        foreach ($scenarios as $scenario) {
            ActivityLog::create([
                'user_id'    => $scenario['user_id'],
                'model_type' => $scenario['model_type'],
                'model_id'   => $scenario['model_id'],
                'action'     => $scenario['action'],
                'old_values' => $scenario['old_values'],
                'new_values' => $scenario['new_values'],
                'changes'    => $scenario['changes'],
                'ip_address' => $scenario['ip_address'],
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/124.0',
                'created_at' => $scenario['created_at'],
                'updated_at' => $scenario['created_at'],
            ]);
        }
    }
}
