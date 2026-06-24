<?php

namespace Database\Seeders;

use App\Models\QuestionBankItem;
use App\Models\QuestionCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuestionBankSampleSeeder extends Seeder
{
    public function run(): void
    {
        $creatorId = User::where('role', 'teacher')->value('id') ?: User::value('id');

        $categories = [];
        foreach ($this->categorySeedData() as $key => $data) {
            $categories[$key] = QuestionCategory::updateOrCreate(
                [
                    'name' => $data['name'],
                    'grade_level' => $data['grade_level'],
                    'skill_type' => $data['skill_type'],
                    'topic' => $data['topic'],
                ],
                [
                    'is_active' => true,
                ]
            );
        }

        foreach ($this->questionSeedData() as $questionData) {
            $category = $categories[$questionData['category_key']];

            $item = QuestionBankItem::updateOrCreate(
                [
                    'category_id' => $category->id,
                    'question_text' => $questionData['question_text'],
                ],
                [
                    'title' => $questionData['title'],
                    'passage' => $questionData['passage'],
                    'audio_url' => $questionData['audio_url'],
                    'answer_mode' => $questionData['answer_mode'],
                    'context_type' => $questionData['context_type'],
                    'difficulty' => $questionData['difficulty'],
                    'correct_answer' => $questionData['correct_answer'],
                    'explanation' => $questionData['explanation'],
                    'is_active' => true,
                    'created_by' => $creatorId,
                ]
            );

            $item->options()->delete();

            if ($questionData['answer_mode'] === 'select') {
                foreach ($questionData['options'] as $index => $option) {
                    $item->options()->create([
                        'option_text' => $option['text'],
                        'is_correct' => $option['is_correct'],
                        'order_index' => $index + 1,
                    ]);
                }
            }
        }

        if ($this->command) {
            $this->command->info('Question bank sample data seeded successfully.');
            $this->command->table(
                ['Type', 'Count'],
                [
                    ['Question categories', QuestionCategory::count()],
                    ['Question bank items', QuestionBankItem::count()],
                ]
            );
        }
    }

    private function categorySeedData(): array
    {
        return [
            'g6_vocab_school' => [
                'name' => 'Từ vựng đồ dùng học tập',
                'grade_level' => 6,
                'skill_type' => 'vocabulary',
                'topic' => 'Trường học',
            ],
            'g6_grammar_daily' => [
                'name' => 'Thì hiện tại đơn',
                'grade_level' => 6,
                'skill_type' => 'grammar',
                'topic' => 'Sinh hoạt hằng ngày',
            ],
            'g7_reading_hobbies' => [
                'name' => 'Đọc hiểu về sở thích',
                'grade_level' => 7,
                'skill_type' => 'reading',
                'topic' => 'Sở thích',
            ],
            'g7_listening_directions' => [
                'name' => 'Nghe chỉ đường',
                'grade_level' => 7,
                'skill_type' => 'listening',
                'topic' => 'Chỉ đường',
            ],
            'g8_writing_weekend' => [
                'name' => 'Viết về cuối tuần trước',
                'grade_level' => 8,
                'skill_type' => 'writing',
                'topic' => 'Cuối tuần',
            ],
            'g8_reading_environment' => [
                'name' => 'Đọc hiểu môi trường',
                'grade_level' => 8,
                'skill_type' => 'reading',
                'topic' => 'Môi trường',
            ],
            'g9_grammar_passive' => [
                'name' => 'Câu bị động',
                'grade_level' => 9,
                'skill_type' => 'grammar',
                'topic' => 'Cấu trúc ngữ pháp',
            ],
            'g9_listening_travel' => [
                'name' => 'Nghe hội thoại du lịch',
                'grade_level' => 9,
                'skill_type' => 'listening',
                'topic' => 'Du lịch',
            ],
        ];
    }

    private function questionSeedData(): array
    {
        return [
            [
                'category_key' => 'g6_vocab_school',
                'title' => 'School things 1',
                'question_text' => 'Which word means "cục tẩy"?',
                'passage' => null,
                'audio_url' => null,
                'answer_mode' => 'select',
                'context_type' => 'normal',
                'difficulty' => 'easy',
                'correct_answer' => null,
                'explanation' => '"Eraser" là từ chỉ cục tẩy.',
                'options' => [
                    ['text' => 'Eraser', 'is_correct' => true],
                    ['text' => 'Notebook', 'is_correct' => false],
                    ['text' => 'Ruler', 'is_correct' => false],
                    ['text' => 'School bag', 'is_correct' => false],
                ],
            ],
            [
                'category_key' => 'g6_vocab_school',
                'title' => 'School things 2',
                'question_text' => 'Write the English word for "bảng".',
                'passage' => null,
                'audio_url' => null,
                'answer_mode' => 'input',
                'context_type' => 'normal',
                'difficulty' => 'easy',
                'correct_answer' => 'blackboard',
                'explanation' => 'Câu trả lời mẫu là "blackboard".',
                'options' => [],
            ],
            [
                'category_key' => 'g6_vocab_school',
                'title' => 'School things 3',
                'question_text' => 'Choose the correct word: I put my books in my ___.',
                'passage' => null,
                'audio_url' => null,
                'answer_mode' => 'select',
                'context_type' => 'normal',
                'difficulty' => 'easy',
                'correct_answer' => null,
                'explanation' => 'Sách thường được để trong cặp.',
                'options' => [
                    ['text' => 'backpack', 'is_correct' => true],
                    ['text' => 'pencil', 'is_correct' => false],
                    ['text' => 'eraser', 'is_correct' => false],
                    ['text' => 'clock', 'is_correct' => false],
                ],
            ],
            [
                'category_key' => 'g6_grammar_daily',
                'title' => 'Present simple 1',
                'question_text' => 'Lan ___ to school at 6:30 every morning.',
                'passage' => null,
                'audio_url' => null,
                'answer_mode' => 'select',
                'context_type' => 'normal',
                'difficulty' => 'easy',
                'correct_answer' => null,
                'explanation' => 'Chủ ngữ số ít ở hiện tại đơn dùng động từ thêm "-es".',
                'options' => [
                    ['text' => 'go', 'is_correct' => false],
                    ['text' => 'goes', 'is_correct' => true],
                    ['text' => 'going', 'is_correct' => false],
                    ['text' => 'went', 'is_correct' => false],
                ],
            ],
            [
                'category_key' => 'g6_grammar_daily',
                'title' => 'Present simple 2',
                'question_text' => 'Complete the sentence: They ___ soccer after school every day.',
                'passage' => null,
                'audio_url' => null,
                'answer_mode' => 'input',
                'context_type' => 'normal',
                'difficulty' => 'easy',
                'correct_answer' => 'play',
                'explanation' => 'Với chủ ngữ "They", dùng động từ nguyên mẫu "play".',
                'options' => [],
            ],
            [
                'category_key' => 'g6_grammar_daily',
                'title' => 'Present simple 3',
                'question_text' => 'Which sentence is correct?',
                'passage' => null,
                'audio_url' => null,
                'answer_mode' => 'select',
                'context_type' => 'normal',
                'difficulty' => 'medium',
                'correct_answer' => null,
                'explanation' => 'Chủ ngữ "He" dùng động từ thêm "-es".',
                'options' => [
                    ['text' => 'He brush his teeth every night.', 'is_correct' => false],
                    ['text' => 'He brushes his teeth every night.', 'is_correct' => true],
                    ['text' => 'He brushed his teeth every night.', 'is_correct' => false],
                    ['text' => 'He brushing his teeth every night.', 'is_correct' => false],
                ],
            ],
            [
                'category_key' => 'g6_grammar_daily',
                'title' => 'Present simple 4',
                'question_text' => 'Choose the correct form: My brother ___ TV after dinner.',
                'passage' => null,
                'audio_url' => null,
                'answer_mode' => 'select',
                'context_type' => 'normal',
                'difficulty' => 'easy',
                'correct_answer' => null,
                'explanation' => 'Chủ ngữ số ít nên dùng động từ thêm "-es": watches.',
                'options' => [
                    ['text' => 'watch', 'is_correct' => false],
                    ['text' => 'watches', 'is_correct' => true],
                    ['text' => 'watching', 'is_correct' => false],
                    ['text' => 'watched', 'is_correct' => false],
                ],
            ],
            [
                'category_key' => 'g6_grammar_daily',
                'title' => 'Present simple 5',
                'question_text' => 'Fill in the blank: She ___ her homework every evening. (do)',
                'passage' => null,
                'audio_url' => null,
                'answer_mode' => 'input',
                'context_type' => 'normal',
                'difficulty' => 'easy',
                'correct_answer' => 'does',
                'explanation' => 'Với "she", động từ "do" đổi thành "does".',
                'options' => [],
            ],
            [
                'category_key' => 'g6_grammar_daily',
                'title' => 'Present simple 6',
                'question_text' => 'Which adverb is often used with the present simple tense?',
                'passage' => null,
                'audio_url' => null,
                'answer_mode' => 'select',
                'context_type' => 'normal',
                'difficulty' => 'medium',
                'correct_answer' => null,
                'explanation' => '"Usually" là trạng từ tần suất thường dùng với hiện tại đơn.',
                'options' => [
                    ['text' => 'Yesterday', 'is_correct' => false],
                    ['text' => 'Usually', 'is_correct' => true],
                    ['text' => 'Tomorrow', 'is_correct' => false],
                    ['text' => 'Last night', 'is_correct' => false],
                ],
            ],
            [
                'category_key' => 'g6_vocab_school',
                'title' => 'School things 4',
                'question_text' => 'Which item do you use to cut paper?',
                'passage' => null,
                'audio_url' => null,
                'answer_mode' => 'select',
                'context_type' => 'normal',
                'difficulty' => 'easy',
                'correct_answer' => null,
                'explanation' => 'Scissors là dụng cụ dùng để cắt giấy.',
                'options' => [
                    ['text' => 'Glue', 'is_correct' => false],
                    ['text' => 'Scissors', 'is_correct' => true],
                    ['text' => 'Notebook', 'is_correct' => false],
                    ['text' => 'Compass', 'is_correct' => false],
                ],
            ],
            [
                'category_key' => 'g6_vocab_school',
                'title' => 'School things 5',
                'question_text' => 'Write the English word for "thước kẻ".',
                'passage' => null,
                'audio_url' => null,
                'answer_mode' => 'input',
                'context_type' => 'normal',
                'difficulty' => 'easy',
                'correct_answer' => 'ruler',
                'explanation' => 'Đáp án mẫu là "ruler".',
                'options' => [],
            ],
            [
                'category_key' => 'g7_reading_hobbies',
                'title' => 'Reading hobbies 1',
                'question_text' => 'Why does Minh enjoy collecting stamps?',
                'passage' => 'Minh loves collecting stamps in his free time. He has stamps from Vietnam, Japan, and Australia. He says the hobby helps him learn about different countries and famous places.',
                'audio_url' => null,
                'answer_mode' => 'select',
                'context_type' => 'reading',
                'difficulty' => 'medium',
                'correct_answer' => null,
                'explanation' => 'Bài đọc nói rõ sở thích này giúp Minh học về các quốc gia khác nhau.',
                'options' => [
                    ['text' => 'Because it helps him learn about different countries.', 'is_correct' => true],
                    ['text' => 'Because his mother forces him to do it.', 'is_correct' => false],
                    ['text' => 'Because he can sell stamps every weekend.', 'is_correct' => false],
                    ['text' => 'Because he does not like reading books.', 'is_correct' => false],
                ],
            ],
            [
                'category_key' => 'g7_reading_hobbies',
                'title' => 'Reading hobbies 2',
                'question_text' => 'What does Mai usually grow in her garden?',
                'passage' => 'Mai spends a lot of time in her small garden behind the house. She waters the plants every afternoon and often grows tomatoes, cucumbers, and some herbs for her family meals.',
                'audio_url' => null,
                'answer_mode' => 'input',
                'context_type' => 'reading',
                'difficulty' => 'medium',
                'correct_answer' => 'tomatoes, cucumbers, and herbs',
                'explanation' => 'Có thể chấm linh hoạt theo ý đúng, đáp án mẫu gồm tomatoes, cucumbers, and herbs.',
                'options' => [],
            ],
            [
                'category_key' => 'g7_listening_directions',
                'title' => 'Listening directions 1',
                'question_text' => 'According to the audio, where should Nam turn first to get to the bookstore?',
                'passage' => null,
                'audio_url' => 'https://example.com/audio/g7-directions-turn-left.mp3',
                'answer_mode' => 'select',
                'context_type' => 'listening',
                'difficulty' => 'medium',
                'correct_answer' => null,
                'explanation' => 'Đáp án mẫu giả định trong file nghe là turn left first.',
                'options' => [
                    ['text' => 'Turn left at the traffic lights.', 'is_correct' => true],
                    ['text' => 'Turn right at the bakery.', 'is_correct' => false],
                    ['text' => 'Go straight for two kilometers.', 'is_correct' => false],
                    ['text' => 'Go back to the bus station.', 'is_correct' => false],
                ],
            ],
            [
                'category_key' => 'g7_listening_directions',
                'title' => 'Listening directions 2',
                'question_text' => 'What place is next to the bakery in the audio?',
                'passage' => null,
                'audio_url' => 'https://example.com/audio/g7-directions-bakery.mp3',
                'answer_mode' => 'input',
                'context_type' => 'listening',
                'difficulty' => 'medium',
                'correct_answer' => 'post office',
                'explanation' => 'Đáp án mẫu là "post office".',
                'options' => [],
            ],
            [
                'category_key' => 'g8_writing_weekend',
                'title' => 'Past simple writing 1',
                'question_text' => 'Write one correct past simple form of the verb "go".',
                'passage' => null,
                'audio_url' => null,
                'answer_mode' => 'input',
                'context_type' => 'normal',
                'difficulty' => 'easy',
                'correct_answer' => 'went',
                'explanation' => 'Động từ quá khứ của "go" là "went".',
                'options' => [],
            ],
            [
                'category_key' => 'g8_writing_weekend',
                'title' => 'Past simple writing 2',
                'question_text' => 'Complete the sentence in the past simple: Last Sunday, we ___ football in the park.',
                'passage' => null,
                'audio_url' => null,
                'answer_mode' => 'input',
                'context_type' => 'normal',
                'difficulty' => 'easy',
                'correct_answer' => 'played',
                'explanation' => 'Câu có dấu hiệu quá khứ nên dùng "played".',
                'options' => [],
            ],
            [
                'category_key' => 'g8_reading_environment',
                'title' => 'Reading environment 1',
                'question_text' => 'What did the students collect during recycling day?',
                'passage' => 'Every month, Green School holds a recycling day. Students bring old newspapers, plastic bottles, and empty cans from home. The class with the most useful recycled items wins a small prize.',
                'audio_url' => null,
                'answer_mode' => 'select',
                'context_type' => 'reading',
                'difficulty' => 'medium',
                'correct_answer' => null,
                'explanation' => 'Bài đọc nêu rõ học sinh mang newspapers, plastic bottles, và empty cans.',
                'options' => [
                    ['text' => 'Old newspapers, plastic bottles, and empty cans', 'is_correct' => true],
                    ['text' => 'Only glass bottles and paper bags', 'is_correct' => false],
                    ['text' => 'New books and notebooks', 'is_correct' => false],
                    ['text' => 'Food and clothes', 'is_correct' => false],
                ],
            ],
            [
                'category_key' => 'g8_reading_environment',
                'title' => 'Reading environment 2',
                'question_text' => 'How often does Green School hold recycling day?',
                'passage' => 'Every month, Green School holds a recycling day. Students bring old newspapers, plastic bottles, and empty cans from home. The class with the most useful recycled items wins a small prize.',
                'audio_url' => null,
                'answer_mode' => 'input',
                'context_type' => 'reading',
                'difficulty' => 'easy',
                'correct_answer' => 'every month',
                'explanation' => 'Tần suất là "every month".',
                'options' => [],
            ],
            [
                'category_key' => 'g9_grammar_passive',
                'title' => 'Passive voice 1',
                'question_text' => 'Choose the correct passive sentence for: People speak English all over the world.',
                'passage' => null,
                'audio_url' => null,
                'answer_mode' => 'select',
                'context_type' => 'normal',
                'difficulty' => 'medium',
                'correct_answer' => null,
                'explanation' => 'Câu bị động đúng là "English is spoken all over the world."',
                'options' => [
                    ['text' => 'English is spoken all over the world.', 'is_correct' => true],
                    ['text' => 'English speaks all over the world.', 'is_correct' => false],
                    ['text' => 'English was spoken all over the world every day.', 'is_correct' => false],
                    ['text' => 'English is speak all over the world.', 'is_correct' => false],
                ],
            ],
            [
                'category_key' => 'g9_grammar_passive',
                'title' => 'Passive voice 2',
                'question_text' => 'Complete the passive sentence: The homework ___ by Lan yesterday. (finish)',
                'passage' => null,
                'audio_url' => null,
                'answer_mode' => 'input',
                'context_type' => 'normal',
                'difficulty' => 'medium',
                'correct_answer' => 'was finished',
                'explanation' => 'Dấu hiệu quá khứ "yesterday" nên dùng "was finished".',
                'options' => [],
            ],
            [
                'category_key' => 'g9_listening_travel',
                'title' => 'Listening travel 1',
                'question_text' => 'What time will the bus leave according to the conversation?',
                'passage' => null,
                'audio_url' => 'https://example.com/audio/g9-travel-bus-time.mp3',
                'answer_mode' => 'select',
                'context_type' => 'listening',
                'difficulty' => 'medium',
                'correct_answer' => null,
                'explanation' => 'Đáp án mẫu trong file nghe là 7:30 a.m.',
                'options' => [
                    ['text' => 'At 6:45 a.m.', 'is_correct' => false],
                    ['text' => 'At 7:30 a.m.', 'is_correct' => true],
                    ['text' => 'At 8:15 a.m.', 'is_correct' => false],
                    ['text' => 'At 9:00 a.m.', 'is_correct' => false],
                ],
            ],
            [
                'category_key' => 'g9_listening_travel',
                'title' => 'Listening travel 2',
                'question_text' => 'Which city are they planning to visit first?',
                'passage' => null,
                'audio_url' => 'https://example.com/audio/g9-travel-first-city.mp3',
                'answer_mode' => 'input',
                'context_type' => 'listening',
                'difficulty' => 'medium',
                'correct_answer' => 'Da Nang',
                'explanation' => 'Đáp án mẫu là Da Nang.',
                'options' => [],
            ],
        ];
    }
}
