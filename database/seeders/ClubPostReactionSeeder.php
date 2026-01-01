<?php

namespace Database\Seeders;

use App\Models\ClubPost;
use App\Models\ClubPostReaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClubPostReactionSeeder extends Seeder
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

        $reactionTypes = ['like', 'heart', 'haha'];

        foreach ($posts as $post) {
            // Mỗi bài đăng có 3-8 reactions
            $reactionCount = rand(3, 8);
            
            // Lấy users ngẫu nhiên để reaction (không trùng với người đăng)
            $users = User::where('id', '!=', $post->user_id)
                         ->inRandomOrder()
                         ->limit($reactionCount)
                         ->get();

            foreach ($users as $user) {
                ClubPostReaction::create([
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                    'type' => $reactionTypes[array_rand($reactionTypes)],
                    'created_at' => $post->created_at->addMinutes(rand(1, 60)),
                ]);
            }
        }
    }
}

