<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Services\SessionService;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    private SessionService $sessionService;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function create(int $classId)
    {
        $class = SchoolClass::findOrFail($classId);
        return view('admin.sessions.create', compact('class'));
    }

    public function store(Request $request, int $classId)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'session_date' => 'required|date',
        ]);
        $data['class_id'] = $classId;

        $this->sessionService->createSession($data);
        return redirect()->route('admin.classes.show', $classId)
            ->with('success', 'Thêm buổi học thành công!');
    }

    public function destroy(Request $request, int $id)
    {
        $session = \App\Models\ClassSession::findOrFail($id);
        $classId = $session->class_id;
        $this->sessionService->deleteSession($id);
        return redirect()->route('admin.classes.show', $classId)
            ->with('success', 'Xóa buổi học thành công!');
    }

    public function complete(int $id)
    {
        $session = $this->sessionService->markCompleted($id);
        return redirect()->route('admin.classes.show', $session->class_id)
            ->with('success', 'Danh dau buoi hoc hoan thanh.');
    }
}

