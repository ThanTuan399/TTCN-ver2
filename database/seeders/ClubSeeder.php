<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClubSeeder extends Seeder
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

        $clubs = [
            [
                'name' => 'Câu lạc bộ Lập trình',
                'description' => 'Nơi các bạn yêu thích lập trình cùng nhau học hỏi và phát triển kỹ năng.',
                'avatar' => null,
                'cover_image' => null,
                'founded_date' => '2020-01-15',
                'status' => 'active',
                'created_by' => $users->random()->id,
            ],
            [
                'name' => 'Câu lạc bộ Tiếng Anh',
                'description' => 'Cải thiện kỹ năng tiếng Anh thông qua các hoạt động giao lưu và học tập.',
                'avatar' => null,
                'cover_image' => null,
                'founded_date' => '2019-09-01',
                'status' => 'active',
                'created_by' => $users->random()->id,
            ],
            [
                'name' => 'Câu lạc bộ Nhiếp ảnh',
                'description' => 'Chia sẻ đam mê nhiếp ảnh và học hỏi kỹ thuật chụp ảnh chuyên nghiệp.',
                'avatar' => null,
                'cover_image' => null,
                'founded_date' => '2021-03-20',
                'status' => 'active',
                'created_by' => $users->random()->id,
            ],
            [
                'name' => 'Câu lạc bộ Thể thao',
                'description' => 'Tổ chức các hoạt động thể thao, rèn luyện sức khỏe cho sinh viên.',
                'avatar' => null,
                'cover_image' => null,
                'founded_date' => '2018-11-10',
                'status' => 'active',
                'created_by' => $users->random()->id,
            ],
            [
                'name' => 'Câu lạc bộ Tình nguyện',
                'description' => 'Tham gia các hoạt động tình nguyện, giúp đỡ cộng đồng.',
                'avatar' => null,
                'cover_image' => null,
                'founded_date' => '2020-05-01',
                'status' => 'active',
                'created_by' => $users->random()->id,
            ],
            [
                'name' => 'Câu lạc bộ Âm nhạc',
                'description' => 'Nơi các bạn yêu thích âm nhạc cùng nhau biểu diễn và sáng tác.',
                'avatar' => null,
                'cover_image' => null,
                'founded_date' => '2022-01-10',
                'status' => 'inactive',
                'created_by' => $users->random()->id,
            ],
        ];

        foreach ($clubs as $club) {
            Club::create($club);
        }
    }
}

