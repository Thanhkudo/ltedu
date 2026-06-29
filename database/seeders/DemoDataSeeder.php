<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\ClassSession;
use App\Models\Exercise;
use App\Models\Assignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        //  1. Tao Users 
        $admin = User::create([
            'name'     => 'Admin He thong',
            'email'    => 'admin@school.test',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        $teacher = User::create([
            'name'     => 'Giao vien Nguyen Van A',
            'email'    => 'teacher@school.test',
            'password' => Hash::make('password'),
            'role'     => 'teacher',
        ]);

        //  2. Tao hoc vien 
        $students = collect([
            ['full_name' => 'Tran Thi B', 'email' => 'b@school.test', 'student_code' => 'HV0001'],
            ['full_name' => 'Le Van C',   'email' => 'c@school.test', 'student_code' => 'HV0002'],
            ['full_name' => 'Pham Thi D', 'email' => 'd@school.test', 'student_code' => 'HV0003'],
        ])->map(fn ($data) => Student::create($data));

        //  3. Tao lop hoc 
        $class = SchoolClass::create([
            'class_code' => 'LOP-001',
            'name'       => 'Tieng Anh Giao tiep K1',
            'teacher_id' => $teacher->id,
            'start_date' => '2024-02-01',
            'end_date'   => '2024-06-30',
            'status'     => 'active',
        ]);

        // Enroll hoc vien vao lop (qua pivot table)
        $class->students()->attach(
            $students->pluck('id')->mapWithKeys(fn ($id) => [
                $id => ['enrolled_at' => now(), 'status' => 'active'],
            ])->all()
        );

        //  4. Tao buoi hoc 
        $session1 = ClassSession::create([
            'class_id'       => $class->id,
            'session_number' => 1,
            'title'          => 'Gioi thieu khoa hoc & Phat am co ban',
            'session_date'   => '2024-02-05 08:00:00',
            'status'         => 'completed',
        ]);

        $session2 = ClassSession::create([
            'class_id'       => $class->id,
            'session_number' => 2,
            'title'          => 'Chu de: Gia dinh va ban be',
            'session_date'   => '2024-02-12 08:00:00',
            'status'         => 'scheduled',
        ]);

        //  5. Tao bai tap thu vien 
        $exercise = Exercise::create([
            'title'      => 'Viet doan van gioi thieu ban than',
            'content'    => 'Viet mot doan van 100-150 tu gioi thieu ve ban than, gia dinh va so thich cua ban.',
            'type'       => 'writing',
            'difficulty' => 'easy',
            'created_by' => $teacher->id,
        ]);

        //  6. Giao bai tap 
        Assignment::create([
            'session_id'   => $session1->id,
            'exercise_id'  => $exercise->id,
            'instructions' => 'Nop bai truoc buoi hoc tiep theo.',
            'due_date'     => '2024-02-11 23:59:59',
            'max_score'    => 100,
        ]);


        $this->command->info('Demo data seeded successfully!');
        $this->command->table(
            ['Account', 'Email', 'Password'],
            [
                ['Admin',    'admin@school.test',   'password'],
                ['Teacher',  'teacher@school.test', 'password'],
            ]
        );
    }
}
