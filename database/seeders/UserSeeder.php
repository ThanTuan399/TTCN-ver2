<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'student_code' => 'SV001',
            'avatar' => null,
            'status' => 'active',
        ]);

        // Tạo một số users mẫu
        $users = [
            [
                'name' => 'Nguyễn Văn A',
                'email' => 'nguyenvana@example.com',
                'password' => Hash::make('password'),
                'student_code' => 'SV002',
                'status' => 'active',
            ],
            [
                'name' => 'Trần Thị B',
                'email' => 'tranthib@example.com',
                'password' => Hash::make('password'),
                'student_code' => 'SV003',
                'status' => 'active',
            ],
            [
                'name' => 'Lê Văn C',
                'email' => 'levanc@example.com',
                'password' => Hash::make('password'),
                'student_code' => 'SV004',
                'status' => 'active',
            ],
            [
                'name' => 'Phạm Thị D',
                'email' => 'phamthid@example.com',
                'password' => Hash::make('password'),
                'student_code' => 'SV005',
                'status' => 'active',
            ],
            [
                'name' => 'Hoàng Văn E',
                'email' => 'hoangvane@example.com',
                'password' => Hash::make('password'),
                'student_code' => 'SV006',
                'status' => 'active',
            ],
            [
                'name' => 'Vũ Thị F',
                'email' => 'vuthif@example.com',
                'password' => Hash::make('password'),
                'student_code' => 'SV007',
                'status' => 'banned',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        // Tạo thêm users ngẫu nhiên
        User::factory(20)->create();
    }
}

