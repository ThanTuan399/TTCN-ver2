<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\ClubMember;
use App\Models\ClubPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClubPostSeeder extends Seeder
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

        $postContents = [
            'Chào mừng các bạn đến với câu lạc bộ! Hãy cùng nhau phát triển và học hỏi nhé!',
            'Thông báo về buổi họp mặt tuần này. Mời tất cả thành viên tham gia.',
            'Chia sẻ một số tài liệu học tập hữu ích cho các bạn.',
            'Cảm ơn các bạn đã tham gia hoạt động vừa qua. Chúng ta đã có những trải nghiệm tuyệt vời!',
            'Thông báo về sự kiện sắp tới. Đừng bỏ lỡ cơ hội tham gia nhé!',
            'Chia sẻ kinh nghiệm học tập và làm việc nhóm hiệu quả.',
            'Tìm kiếm thành viên cho dự án mới. Ai quan tâm có thể liên hệ nhé!',
            'Cập nhật về các hoạt động trong tháng này. Hãy theo dõi để không bỏ lỡ!',
        ];

        foreach ($clubs as $club) {
            // Lấy các thành viên đã được approved
            $approvedMembers = ClubMember::where('club_id', $club->id)
                                         ->where('status', 'approved')
                                         ->pluck('user_id');

            if ($approvedMembers->isEmpty()) {
                continue;
            }

            // Tạo 5-10 bài đăng cho mỗi club
            $postCount = rand(5, 10);
            for ($i = 0; $i < $postCount; $i++) {
                $user = User::find($approvedMembers->random());
                
                ClubPost::create([
                    'club_id' => $club->id,
                    'user_id' => $user->id,
                    'content' => $postContents[array_rand($postContents)] . ' ' . fake()->sentence(),
                    'is_anonymous' => rand(0, 10) < 2, // 20% là anonymous
                    'status' => ['pending', 'approved', 'approved', 'approved'][array_rand(['pending', 'approved', 'approved', 'approved'])],
                    'created_at' => now()->subDays(rand(0, 30)),
                ]);
            }
        }
    }
}

