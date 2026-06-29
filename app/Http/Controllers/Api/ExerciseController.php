<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExerciseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{
    private ExerciseService $exerciseService;

    public function __construct(ExerciseService $exerciseService)
    {
        $this->exerciseService = $exerciseService;
    }

    /**
     * GET /api/exercises?type=writing&difficulty=medium&search=keyword
     */
    public function index(Request $request): JsonResponse
    {
        $exercises = $this->exerciseService->listExercises(
            $request->only('search', 'type', 'difficulty')
        );
        return response()->json(['data' => $exercises]);
    }

    /**
     * POST /api/exercises
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'content'     => 'required|string',
            'type'        => 'required|in:reading,writing,listening,speaking,grammar,vocabulary',
            'difficulty'  => 'nullable|in:easy,medium,hard',
        ]);

        // Tam thoi dung user_id = 1 (thuc te lay tu Auth::id())
        $exercise = $this->exerciseService->createExercise($data, optional($request->user())->id ?? 1);
        return response()->json(['data' => $exercise, 'message' => 'Tao bai tap thanh cong.'], 201);
    }

    /**
     * GET /api/exercises/{id}
     */
    public function show(int $id): JsonResponse
    {
        $exercise = $this->exerciseService->getExercise($id);
        return response()->json(['data' => $exercise]);
    }

    /**
     * PUT /api/exercises/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'content'     => 'sometimes|required|string',
            'type'        => 'sometimes|in:reading,writing,listening,speaking,grammar,vocabulary',
            'difficulty'  => 'nullable|in:easy,medium,hard',
        ]);

        $exercise = $this->exerciseService->updateExercise($id, $data);
        return response()->json(['data' => $exercise, 'message' => 'Cap nhat bai tap thanh cong.']);
    }

    /**
     * DELETE /api/exercises/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $this->exerciseService->deleteExercise($id);
        return response()->json(['message' => 'Xoa bai tap thanh cong.']);
    }
}
