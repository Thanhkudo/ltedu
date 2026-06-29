<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\Student;
use App\Http\Controllers\Admin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Student / Public routes
Route::get('/', [Student\DashboardController::class, 'index'])->name('student.home');
Route::post('/pick-student', [Student\DashboardController::class, 'pickStudent'])->name('student.pick');
Route::post('/logout-student', [Student\DashboardController::class, 'logout'])->name('student.logout');
Route::get('/huong-dan', [GuideController::class, 'student'])->name('guide');

Route::get('/language/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['en', 'vi'], true), 404);

    session(['locale' => $locale]);

    return redirect()->back();
})->name('language.switch');

Route::get('/classes/{id}', [Student\StudentClassController::class, 'show'])->name('student.classes.show');

Route::get('/assignments/{id}', [Student\StudentAssignmentController::class, 'show'])->name('student.assignments.show');
Route::get('/assignments/{id}/practice', [Student\StudentAssignmentController::class, 'practice'])->name('student.assignments.practice');
Route::post('/assignments/{id}/submit', [Student\StudentAssignmentController::class, 'submit'])->name('student.assignments.submit');

// Admin routes
Route::prefix('admin')->name('admin.')->middleware('admin.locale')->group(function () {

    // Auth
    Route::middleware('guest')->group(function () {
        Route::get('/login', [Admin\AuthController::class, 'showLogin'])->name('login.form');
        Route::post('/login', [Admin\AuthController::class, 'login'])->name('login');
    });

    Route::get('/register', [Admin\AuthController::class, 'showRegister'])->name('register.form');
    Route::post('/register', [Admin\AuthController::class, 'register'])->name('register');

    Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout')->middleware('admin.access');

    Route::middleware('admin.access')->group(function () {

    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard')->middleware('admin.module:dashboard');
    Route::get('huong-dan', [GuideController::class, 'admin'])->name('guide');

    // Users
    Route::resource('users', Admin\UserController::class)->middleware('admin.access');

    // Students
    Route::resource('students', Admin\StudentController::class)->middleware('admin.module:students');

    // Classes
    Route::resource('classes', Admin\ClassController::class)->middleware('admin.module:classes');
    Route::post('classes/{id}/enroll', [Admin\ClassController::class, 'enroll'])->name('classes.enroll')->middleware('admin.module:classes');
    Route::post('classes/{id}/drop/{studentId}', [Admin\ClassController::class, 'dropStudent'])->name('classes.drop')->middleware('admin.module:classes');

    // Sessions (nested under a class)
    Route::get('classes/{classId}/sessions/create', [Admin\SessionController::class, 'create'])->name('sessions.create')->middleware('admin.module:sessions');
    Route::post('classes/{classId}/sessions', [Admin\SessionController::class, 'store'])->name('sessions.store')->middleware('admin.module:sessions');
    Route::delete('sessions/{id}', [Admin\SessionController::class, 'destroy'])->name('sessions.destroy')->middleware('admin.module:sessions');
    Route::post('sessions/{id}/complete', [Admin\SessionController::class, 'complete'])->name('sessions.complete')->middleware('admin.module:sessions');

    // Assignments
    Route::get('assignments/create', [Admin\AssignmentController::class, 'create'])->name('assignments.create')->middleware('admin.module:assignments');
    Route::post('assignments', [Admin\AssignmentController::class, 'store'])->name('assignments.store')->middleware('admin.module:assignments');
    Route::delete('assignments/{id}', [Admin\AssignmentController::class, 'destroy'])->name('assignments.destroy')->middleware('admin.module:assignments');
    Route::get('assignments/{id}/submissions', [Admin\AssignmentController::class, 'submissions'])->name('assignments.submissions')->middleware('admin.module:assignments');
    Route::post('assignments/{id}/submissions/{subId}/grade', [Admin\AssignmentController::class, 'grade'])->name('assignments.grade')->middleware('admin.module:assignments');



    // Question bank
    Route::get('question-bank', [Admin\QuestionBankController::class, 'index'])
        ->name('question-bank.index')->middleware('admin.module:question-bank');
    Route::get('question-bank/create', [Admin\QuestionBankController::class, 'create'])
        ->name('question-bank.create')->middleware('admin.module:question-bank');
    Route::post('question-bank', [Admin\QuestionBankController::class, 'store'])
        ->name('question-bank.store')->middleware('admin.module:question-bank');
    Route::get('question-bank/import', [Admin\QuestionBankController::class, 'importForm'])
        ->name('question-bank.import')->middleware('admin.module:question-bank');
    Route::get('question-bank/import/template', [Admin\QuestionBankController::class, 'downloadImportTemplate'])
        ->name('question-bank.import-template')->middleware('admin.module:question-bank');
    Route::post('question-bank/import', [Admin\QuestionBankController::class, 'import'])
        ->name('question-bank.import.store')->middleware('admin.module:question-bank');
    Route::get('question-bank/{id}/edit', [Admin\QuestionBankController::class, 'edit'])
        ->name('question-bank.edit')->middleware('admin.module:question-bank');
    Route::put('question-bank/{id}', [Admin\QuestionBankController::class, 'update'])
        ->name('question-bank.update')->middleware('admin.module:question-bank');
    Route::delete('question-bank/{id}', [Admin\QuestionBankController::class, 'destroy'])
        ->name('question-bank.destroy')->middleware('admin.module:question-bank');

    Route::get('question-groups', [Admin\QuestionBankController::class, 'groups'])
        ->name('question-groups.index')->middleware('admin.module:question-bank');
    Route::post('question-groups', [Admin\QuestionBankController::class, 'storeGroup'])
        ->name('question-groups.store')->middleware('admin.module:question-bank');
    Route::put('question-groups/{id}', [Admin\QuestionBankController::class, 'updateGroup'])
        ->name('question-groups.update')->middleware('admin.module:question-bank');
    Route::delete('question-groups/{id}', [Admin\QuestionBankController::class, 'destroyGroup'])
        ->name('question-groups.destroy')->middleware('admin.module:question-bank');

    // Question categories
    Route::get('question-categories', [Admin\QuestionBankController::class, 'categories'])
        ->name('question-categories.index')->middleware('admin.module:question-categories');
    Route::post('question-categories', [Admin\QuestionBankController::class, 'storeCategory'])
        ->name('question-categories.store')->middleware('admin.module:question-categories');
    Route::put('question-categories/{id}', [Admin\QuestionBankController::class, 'updateCategory'])
        ->name('question-categories.update')->middleware('admin.module:question-categories');
    Route::delete('question-categories/{id}', [Admin\QuestionBankController::class, 'destroyCategory'])
        ->name('question-categories.destroy')->middleware('admin.module:question-categories');

    });
});

Route::middleware('admin.access')->group(function () {
    Route::any('/ckfinder/connector', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')
        ->name('ckfinder_connector');
    Route::any('/ckfinder/browser', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')
        ->name('ckfinder_browser');
});
