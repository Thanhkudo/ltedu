<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Exercise;
use App\Models\AssignmentSubmission;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'classes'    => SchoolClass::count(),
            'students'   => Student::count(),
            'exercises'  => Exercise::count(),            'submissions'=> AssignmentSubmission::where('status', 'submitted')->count(),
        ];

        $recentClasses  = SchoolClass::with('teacher')->latest()->take(5)->get();
        $recentStudents = Student::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentClasses', 'recentStudents'));
    }
}
