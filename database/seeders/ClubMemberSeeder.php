<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\ClubMember;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClubMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clubs = Club::all();
        $users = User::all();

        if ($clubs->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No clubs or users found. Please run ClubSeeder and UserSeeder first.');
            return;
        }

        foreach ($clubs as $club) {
            // Người tạo club là owner
            ClubMember::create([
                'club_id' => $club->id,
                'user_id' => $club->created_by,
                'role' => 'owner',
                'status' => 'approved',
                'joined_at' => $club->created_at,
            ]);

            // Tạo một số admin cho mỗi club
            $adminUsers = $users->where('id', '!=', $club->created_by)->random(min(2, $users->count() - 1));
            foreach ($adminUsers as $admin) {
                ClubMember::create([
                    'club_id' => $club->id,
                    'user_id' => $admin->id,
                    'role' => 'admin',
                    'status' => 'approved',
                    'joined_at' => now()->subDays(rand(1, 30)),
                ]);
            }

            // Tạo một số members thường
            $memberUsers = $users->where('id', '!=', $club->created_by)
                                 ->whereNotIn('id', $adminUsers->pluck('id'))
                                 ->random(min(5, $users->count() - 3));

            foreach ($memberUsers as $member) {
                $statuses = ['approved', 'pending', 'approved', 'approved']; // Tỷ lệ approved cao hơn
                ClubMember::create([
                    'club_id' => $club->id,
                    'user_id' => $member->id,
                    'role' => 'member',
                    'status' => $statuses[array_rand($statuses)],
                    'joined_at' => now()->subDays(rand(1, 60)),
                ]);
            }
        }
    }
}

