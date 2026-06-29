<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    private SessionService $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    /**
     * GET /api/classes/{classId}/sessions
     */
    public function index(int $classId): JsonResponse
    {
        $sessions = $this->sessionService->getSessionsByClass($classId);
        return response()->json(['data' => $sessions]);
    }

    /**
     * POST /api/classes/{classId}/sessions
     */
    public function store(Request $request, int $classId): JsonResponse
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'session_date'   => 'required|date',
            'session_number' => 'nullable|integer|min:1',
            'status'         => 'nullable|in:scheduled,completed,cancelled',
        ]);

        $data['class_id'] = $classId;
        $session = $this->sessionService->createSession($data);
        return response()->json(['data' => $session, 'message' => 'Tao buoi hoc thanh cong.'], 201);
    }

    /**
     * GET /api/sessions/{id}
     */
    public function show(int $id): JsonResponse
    {
        $session = $this->sessionService->getSession($id);
        return response()->json(['data' => $session]);
    }

    /**
     * PUT /api/sessions/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'title'        => 'sometimes|required|string|max:255',
            'description'  => 'nullable|string',
            'session_date' => 'sometimes|required|date',
            'status'       => 'nullable|in:scheduled,completed,cancelled',
        ]);

        $session = $this->sessionService->updateSession($id, $data);
        return response()->json(['data' => $session, 'message' => 'Cap nhat buoi hoc thanh cong.']);
    }

    /**
     * DELETE /api/sessions/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $this->sessionService->deleteSession($id);
        return response()->json(['message' => 'Xoa buoi hoc thanh cong.']);
    }

    /**
     * PATCH /api/sessions/{id}/complete
     * Danh dau buoi hoc da hoan thanh.
     */
    public function complete(int $id): JsonResponse
    {
        $session = $this->sessionService->markCompleted($id);
        return response()->json(['data' => $session, 'message' => 'Buoi hoc da hoan thanh.']);
    }
}
