<?php

namespace App\Services;

use App\Models\ClassSession;
use App\Models\SchoolClass;

class SessionService
{
    public function getSessionsByClass(int $classId)
    {
        return ClassSession::where('class_id', $classId)
            ->with(['assignments.exercise'])
            ->orderBy('session_number')
            ->get();
    }

    public function getSession(int $id): ClassSession
    {
        return ClassSession::with(['schoolClass', 'assignments.exercise'])->findOrFail($id);
    }

    public function createSession(array $data): ClassSession
    {
        // Tu tang session_number neu khong truyen
        if (empty($data['session_number'])) {
            $lastSession = ClassSession::where('class_id', $data['class_id'])->max('session_number');
            $data['session_number'] = ($lastSession ?? 0) + 1;
        }

        return ClassSession::create($data);
    }

    public function updateSession(int $id, array $data): ClassSession
    {
        $session = ClassSession::findOrFail($id);
        $session->update($data);
        return $session->fresh();
    }

    public function deleteSession(int $id): bool
    {
        return ClassSession::findOrFail($id)->delete();
    }

    public function markCompleted(int $id): ClassSession
    {
        $session = ClassSession::findOrFail($id);
        $session->update(['status' => 'completed']);
        return $session;
    }
}
