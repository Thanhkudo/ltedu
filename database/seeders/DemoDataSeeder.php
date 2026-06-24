<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\ClassSession;
use App\Models\Exercise;
use App\Models\Assignment;
use App\Models\SchoolTest;
use App\Models\TestQuestion;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. Tạo Users ─────────────────────────────────────
        $admin = User::create([
            'name'     => 'Admin Hệ thống',
            'email'    => 'admin@school.test',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        $teacher = User::create([
            'name'     => 'Giáo viên Nguyễn Văn A',
            'email'    => 'teacher@school.test',
            'password' => Hash::make('password'),
            'role'     => 'teacher',
        ]);

        // ─── 2. Tạo học viên ──────────────────────────────────
        $students = collect([
            ['full_name' => 'Trần Thị B', 'email' => 'b@school.test', 'student_code' => 'HV0001'],
            ['full_name' => 'Lê Văn C',   'email' => 'c@school.test', 'student_code' => 'HV0002'],
            ['full_name' => 'Phạm Thị D', 'email' => 'd@school.test', 'student_code' => 'HV0003'],
        ])->map(fn ($data) => Student::create($data));

        // ─── 3. Tạo lớp học ────────────────────────────────────
        $class = SchoolClass::create([
            'class_code' => 'LOP-001',
            'name'       => 'Tiếng Anh Giao tiếp K1',
            'teacher_id' => $teacher->id,
            'start_date' => '2024-02-01',
            'end_date'   => '2024-06-30',
            'status'     => 'active',
        ]);

        // Enroll học viên vào lớp (qua pivot table)
        $class->students()->attach(
            $students->pluck('id')->mapWithKeys(fn ($id) => [
                $id => ['enrolled_at' => now(), 'status' => 'active'],
            ])->all()
        );

        // ─── 4. Tạo buổi học ───────────────────────────────────
        $session1 = ClassSession::create([
            'class_id'       => $class->id,
            'session_number' => 1,
            'title'          => 'Giới thiệu khoá học & Phát âm cơ bản',
            'session_date'   => '2024-02-05 08:00:00',
            'status'         => 'completed',
        ]);

        $session2 = ClassSession::create([
            'class_id'       => $class->id,
            'session_number' => 2,
            'title'          => 'Chủ đề: Gia đình và bạn bè',
            'session_date'   => '2024-02-12 08:00:00',
            'status'         => 'scheduled',
        ]);

        // ─── 5. Tạo bài tập thư viện ───────────────────────────
        $exercise = Exercise::create([
            'title'      => 'Viết đoạn văn giới thiệu bản thân',
            'content'    => 'Viết một đoạn văn 100–150 từ giới thiệu về bản thân, gia đình và sở thích của bạn.',
            'type'       => 'writing',
            'difficulty' => 'easy',
            'created_by' => $teacher->id,
        ]);

        // ─── 6. Giao bài tập ───────────────────────────────────
        Assignment::create([
            'session_id'   => $session1->id,
            'exercise_id'  => $exercise->id,
            'instructions' => 'Nộp bài trước buổi học tiếp theo.',
            'due_date'     => '2024-02-11 23:59:59',
            'max_score'    => 100,
        ]);

        // ─── 7. Tạo bài kiểm tra ───────────────────────────────
        $test = SchoolTest::create([
            'class_id'    => $class->id,
            'created_by'  => $teacher->id,
            'title'       => 'Kiểm tra 15 phút – Buổi 1',
            'duration'    => 15,
            'total_score' => 10,
            'starts_at'   => '2024-02-05 08:00:00',
            'ends_at'     => '2024-02-05 08:15:00',
            'status'      => 'published',
        ]);

        // ─── 8. Thêm câu hỏi trắc nghiệm ──────────────────────
        $q1 = TestQuestion::create([
            'test_id'       => $test->id,
            'question_text' => '"Good morning" được dùng vào lúc nào?',
            'question_type' => 'multiple_choice',
            'score'         => 2,
            'order_index'   => 1,
        ]);
        QuestionOption::insert([
            ['question_id' => $q1->id, 'option_text' => 'Buổi sáng',    'is_correct' => true,  'order_index' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $q1->id, 'option_text' => 'Buổi chiều',   'is_correct' => false, 'order_index' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $q1->id, 'option_text' => 'Buổi tối',     'is_correct' => false, 'order_index' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $q1->id, 'option_text' => 'Bất kỳ lúc nào', 'is_correct' => false, 'order_index' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $q2 = TestQuestion::create([
            'test_id'       => $test->id,
            'question_text' => '"How are you?" có nghĩa là "Bạn khoẻ không?"',
            'question_type' => 'true_false',
            'score'         => 2,
            'order_index'   => 2,
        ]);
        QuestionOption::insert([
            ['question_id' => $q2->id, 'option_text' => 'True',  'is_correct' => true,  'order_index' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $q2->id, 'option_text' => 'False', 'is_correct' => false, 'order_index' => 2, 'created_at' => now(), 'updated_at' => now()],
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
