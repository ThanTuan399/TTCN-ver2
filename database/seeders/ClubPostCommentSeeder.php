<?php

namespace Database\Seeders;

use App\Models\ClubPost;
use App\Models\ClubPostComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClubPostCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = ClubPost::where('status', 'approved')->get();

        if ($posts->isEmpty()) {
            $this->command->warn('No approved posts found. Please run ClubPostSeeder first.');
            return;
        }

        $commentTemplates = [
            'Cảm ơn bạn đã chia sẻ!',
            'Rất hữu ích, cảm ơn bạn!',
            'Tôi đồng ý với quan điểm này.',
            'Có thể chia sẻ thêm chi tiết không?',
            'Tuyệt vời! Tôi sẽ tham gia.',
            'Thông tin rất hay, cảm ơn!',
            'Tôi cũng đang quan tâm đến vấn đề này.',
            'Chúc mừng bạn!',
        ];

        foreach ($posts as $post) {
            // Mỗi bài đăng có 2-5 bình luận
            $commentCount = rand(2, 5);
            
            // Lấy users ngẫu nhiên để comment
            $users = User::inRandomOrder()->limit($commentCount)->get();

            foreach ($users as $index => $user) {
                ClubPostComment::create([
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                    'content' => $commentTemplates[array_rand($commentTemplates)] . ' ' . fake()->sentence(),
                    'created_at' => $post->created_at->addHours($index + 1),
                ]);
            }
        }
    }
}

