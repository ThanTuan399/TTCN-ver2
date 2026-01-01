<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\ClubEvent;
use App\Models\ClubMember;
use Illuminate\Database\Seeder;

class ClubEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clubs = Club::all();

        if ($clubs->isEmpty()) {
            $this->command->warn('No clubs found. Please run ClubSeeder first.');
            return;
        }

        $eventTitles = [
            'Họp mặt định kỳ tháng này',
            'Workshop về kỹ năng mềm',
            'Buổi giao lưu với khách mời',
            'Cuộc thi nội bộ',
            'Hoạt động tình nguyện',
            'Chương trình team building',
            'Seminar chuyên đề',
            'Lễ kỷ niệm thành lập',
        ];

        $locations = [
            'Phòng họp A101',
            'Hội trường lớn',
            'Sân thể thao',
            'Thư viện',
            'Phòng đa năng',
            'Khu vực ngoài trời',
            null,
        ];

        foreach ($clubs as $club) {
            // Mỗi club có 2-4 sự kiện
            $eventCount = rand(2, 4);

            // Lấy các thành viên đã được approved để làm người tạo event
            $approvedMembers = ClubMember::where('club_id', $club->id)
                                         ->where('status', 'approved')
                                         ->pluck('user_id');

            if ($approvedMembers->isEmpty()) {
                continue;
            }

            for ($i = 0; $i < $eventCount; $i++) {
                $startTime = now()->addDays(rand(-30, 60));
                $endTime = $startTime->copy()->addHours(rand(2, 4));

                ClubEvent::create([
                    'club_id' => $club->id,
                    'title' => $eventTitles[array_rand($eventTitles)],
                    'description' => fake()->paragraph(3),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'location' => $locations[array_rand($locations)],
                    'created_by' => $approvedMembers->random(),
                    'created_at' => now()->subDays(rand(1, 15)),
                ]);
            }
        }
    }
}

