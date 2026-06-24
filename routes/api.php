<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\SubmissionController;

/*
|--------------------------------------------------------------------------
| API Routes – Hệ thống Quản lý Lớp Học
|--------------------------------------------------------------------------
| Prefix: /api
|
| Quy ước naming:
|   GET    /resource            → index   (danh sách)
|   POST   /resource            → store   (tạo mới)
|   GET    /resource/{id}       → show    (chi tiết)
|   PUT    /resource/{id}       → update  (cập nhật)
|   DELETE /resource/{id}       → destroy (xoá)
*/

// ─── Học viên (Students) ─────────────────────────────────────
Route::apiResource('students', StudentController::class);

// ─── Lớp học (Classes) ───────────────────────────────────────
Route::apiResource('classes', ClassController::class);
Route::post('classes/{id}/enroll', [ClassController::class, 'enroll']);
Route::delete('classes/{id}/students/{studentId}', [ClassController::class, 'dropStudent']);
Route::get('classes/{id}/students', [ClassController::class, 'students']);

// ─── Buổi học (Sessions) – nested dưới classes ───────────────
Route::get('classes/{classId}/sessions', [SessionController::class, 'index']);
Route::post('classes/{classId}/sessions', [SessionController::class, 'store']);
Route::get('sessions/{id}', [SessionController::class, 'show']);
Route::put('sessions/{id}', [SessionController::class, 'update']);
Route::delete('sessions/{id}', [SessionController::class, 'destroy']);
Route::patch('sessions/{id}/complete', [SessionController::class, 'complete']);

// ─── Thư viện bài tập (Exercise Library) ─────────────────────
Route::apiResource('exercises', ExerciseController::class);

// ─── Giao bài tập (Assignments) ──────────────────────────────
Route::apiResource('assignments', AssignmentController::class)->except(['index']);
Route::post('assignments/{id}/submit', [AssignmentController::class, 'submit']);
Route::patch('assignments/{id}/submissions/{submissionId}/grade', [AssignmentController::class, 'grade']);
Route::get('assignments/{id}/submissions', [AssignmentController::class, 'submissions']);

// ─── Bài kiểm tra (Tests) ────────────────────────────────────
Route::get('classes/{classId}/tests', [TestController::class, 'index']);
Route::post('tests', [TestController::class, 'store']);
Route::get('tests/{id}', [TestController::class, 'show']);
Route::put('tests/{id}', [TestController::class, 'update']);
Route::delete('tests/{id}', [TestController::class, 'destroy']);
Route::patch('tests/{id}/publish', [TestController::class, 'publish']);
Route::post('tests/{id}/questions', [TestController::class, 'addQuestion']);
Route::delete('questions/{questionId}', [TestController::class, 'deleteQuestion']);
Route::get('tests/{testId}/submissions', [SubmissionController::class, 'testSubmissions']);

// ─── Làm bài và nộp bài (Submissions) ────────────────────────
Route::post('tests/{testId}/start', [SubmissionController::class, 'startTest']);
Route::patch('submissions/{submissionId}/save', [SubmissionController::class, 'saveAnswers']);
Route::post('submissions/{submissionId}/submit', [SubmissionController::class, 'submitTest']);
Route::patch('answers/{answerId}/grade', [SubmissionController::class, 'gradeAnswer']);

