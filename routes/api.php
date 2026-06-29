<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\AssignmentController;

/*
|--------------------------------------------------------------------------
| API Routes - He thong Quan ly Lop Hoc
|--------------------------------------------------------------------------
| Prefix: /api
|
| Quy uoc naming:
|   GET    /resource            -> index   (danh sach)
|   POST   /resource            -> store   (tao moi)
|   GET    /resource/{id}       -> show    (chi tiet)
|   PUT    /resource/{id}       -> update  (cap nhat)
|   DELETE /resource/{id}       -> destroy (xoa)
*/

//  Hoc vien (Students) 
Route::apiResource('students', StudentController::class);

//  Lop hoc (Classes) 
Route::apiResource('classes', ClassController::class);
Route::post('classes/{id}/enroll', [ClassController::class, 'enroll']);
Route::delete('classes/{id}/students/{studentId}', [ClassController::class, 'dropStudent']);
Route::get('classes/{id}/students', [ClassController::class, 'students']);

//  Buoi hoc (Sessions) - nested duoi classes 
Route::get('classes/{classId}/sessions', [SessionController::class, 'index']);
Route::post('classes/{classId}/sessions', [SessionController::class, 'store']);
Route::get('sessions/{id}', [SessionController::class, 'show']);
Route::put('sessions/{id}', [SessionController::class, 'update']);
Route::delete('sessions/{id}', [SessionController::class, 'destroy']);
Route::patch('sessions/{id}/complete', [SessionController::class, 'complete']);

//  Thu vien bai tap (Exercise Library) 
Route::apiResource('exercises', ExerciseController::class);

//  Giao bai tap (Assignments) 
Route::apiResource('assignments', AssignmentController::class)->except(['index']);
Route::post('assignments/{id}/submit', [AssignmentController::class, 'submit']);
Route::patch('assignments/{id}/submissions/{submissionId}/grade', [AssignmentController::class, 'grade']);
Route::get('assignments/{id}/submissions', [AssignmentController::class, 'submissions']);
