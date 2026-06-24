<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassSession;
use Illuminate\Support\Facades\DB;

class AssignmentService
{
    /**
     * Giao bài tập cho một buổi học.
     */
    public function assignExercise(array $data): Assignment
    {
        return Assignment::create($data);
    }

    public function getAssignment(int $id): Assignment
    {
        return Assignment::with(['session.schoolClass', 'exercise', 'submissions.student'])
            ->findOrFail($id);
    }

    public function updateAssignment(int $id, array $data): Assignment
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->update($data);
        return $assignment->fresh(['session', 'exercise']);
    }

    public function deleteAssignment(int $id): bool
    {
        return Assignment::findOrFail($id)->delete();
    }

    /**
     * Học viên nộp bài tập.
     */
    public function submitAssignment(int $assignmentId, int $studentId, array $data): AssignmentSubmission
    {
        Assignment::findOrFail($assignmentId);

        return DB::transaction(function () use ($assignmentId, $studentId, $data) {
            $submission = AssignmentSubmission::create(array_merge($data, [
                'assignment_id' => $assignmentId,
                'student_id' => $studentId,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]));

            $obsoleteIds = AssignmentSubmission::where('assignment_id', $assignmentId)
                ->where('student_id', $studentId)
                ->orderByDesc('submitted_at')
                ->orderByDesc('id')
                ->skip(3)
                ->pluck('id');

            if ($obsoleteIds->isNotEmpty()) {
                AssignmentSubmission::whereIn('id', $obsoleteIds)->delete();
            }

            return $submission;
        });
    }

    /**
     * Giáo viên chấm bài.
     */
    public function gradeSubmission(int $submissionId, float $score, ?string $feedback = null): AssignmentSubmission
    {
        $submission = AssignmentSubmission::findOrFail($submissionId);

        $assignment = $submission->assignment;
        abort_if($score > $assignment->max_score, 422, "Điểm không được vượt quá {$assignment->max_score}.");

        $submission->update([
            'score'    => $score,
            'feedback' => $feedback,
            'status'   => 'graded',
        ]);

        return $submission;
    }

    /**
     * Lấy danh sách bài nộp của một assignment (để giáo viên xem).
     */
    public function getSubmissionsForAssignment(int $assignmentId)
    {
        return AssignmentSubmission::where('assignment_id', $assignmentId)
            ->with(['student'])
            ->latest('submitted_at')
            ->get();
    }
}
