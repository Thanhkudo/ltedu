<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ExerciseService;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{
    private ExerciseService $exerciseService;

    public function __construct(ExerciseService $exerciseService)
    {
        $this->exerciseService = $exerciseService;
    }

    public function index(Request $request)
    {
        $exercises = $this->exerciseService->listExercises($request->only('search', 'type', 'difficulty'));
        return view('admin.exercises.index', compact('exercises'));
    }

    public function create()
    {
        return view('admin.exercises.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'content'     => 'required|string',
            'type'        => 'required|in:reading,writing,listening,speaking,grammar,vocabulary',
            'difficulty'  => 'nullable|in:easy,medium,hard',
        ]);

        // Demo: dùng teacher đầu tiên trong DB
        $creator = User::where('role', 'teacher')->first() ?? User::first();
        $this->exerciseService->createExercise($data, $creator->id);

        return redirect()->route('admin.exercises.index')
            ->with('success', 'Tạo bài tập thành công!');
    }

    public function edit(int $id)
    {
        $exercise = $this->exerciseService->getExercise($id);
        return view('admin.exercises.edit', compact('exercise'));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'content'     => 'required|string',
            'type'        => 'required|in:reading,writing,listening,speaking,grammar,vocabulary',
            'difficulty'  => 'nullable|in:easy,medium,hard',
        ]);

        $this->exerciseService->updateExercise($id, $data);
        return redirect()->route('admin.exercises.index')
            ->with('success', 'Cập nhật bài tập thành công!');
    }

    public function destroy(int $id)
    {
        $this->exerciseService->deleteExercise($id);
        return redirect()->route('admin.exercises.index')
            ->with('success', 'Xoá bài tập thành công!');
    }
}
