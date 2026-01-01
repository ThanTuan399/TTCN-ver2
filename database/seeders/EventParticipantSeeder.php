<?php

namespace Database\Seeders;

use App\Models\ClubEvent;
use App\Models\EventParticipant;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = ClubEvent::all();

        if ($events->isEmpty()) {
            $this->command->warn('No events found. Please run ClubEventSeeder first.');
            return;
        }

        $statuses = ['going', 'maybe', 'not_going'];
        $statusWeights = ['going' => 60, 'maybe' => 25, 'not_going' => 15]; // Tỷ lệ

        foreach ($events as $event) {
            // Mỗi sự kiện có 5-15 người tham gia
            $participantCount = rand(5, 15);
            
            // Lấy users ngẫu nhiên (không trùng với người tạo)
            $users = User::where('id', '!=', $event->created_by)
                         ->inRandomOrder()
                         ->limit($participantCount)
                         ->get();

            foreach ($users as $user) {
                // Chọn status theo tỷ lệ
                $rand = rand(1, 100);
                $status = 'going';
                if ($rand <= $statusWeights['maybe']) {
                    $status = 'maybe';
                } elseif ($rand <= $statusWeights['maybe'] + $statusWeights['not_going']) {
                    $status = 'not_going';
                }

                EventParticipant::create([
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                    'status' => $status,
                    'created_at' => $event->created_at->addHours(rand(1, 24)),
                ]);
            }
        }
    }
}

