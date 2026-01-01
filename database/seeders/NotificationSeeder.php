<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        $notificationTypes = [
            'post_approved',
            'post_comment',
            'event_reminder',
            'club_invitation',
            'member_request',
            'event_created',
            'system_announcement',
        ];

        $notificationContents = [
            'post_approved' => 'Bài đăng của bạn đã được duyệt.',
            'post_comment' => 'Có người bình luận trên bài đăng của bạn.',
            'event_reminder' => 'Nhắc nhở: Sự kiện sắp diễn ra trong 1 ngày nữa.',
            'club_invitation' => 'Bạn đã được mời tham gia câu lạc bộ.',
            'member_request' => 'Có yêu cầu tham gia câu lạc bộ mới.',
            'event_created' => 'Một sự kiện mới đã được tạo trong câu lạc bộ của bạn.',
            'system_announcement' => 'Thông báo hệ thống: Cập nhật tính năng mới.',
        ];

        foreach ($users as $user) {
            // Mỗi user có 3-8 thông báo
            $notificationCount = rand(3, 8);

            for ($i = 0; $i < $notificationCount; $i++) {
                $type = $notificationTypes[array_rand($notificationTypes)];
                
                Notification::create([
                    'user_id' => $user->id,
                    'content' => $notificationContents[$type] . ' ' . fake()->sentence(),
                    'type' => $type,
                    'is_read' => rand(0, 10) < 7, // 70% đã đọc
                    'created_at' => now()->subDays(rand(0, 7)),
                ]);
            }
        }
    }
}

