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

        $data = $this->normalizeStatusTimestamps($data);

        return ClassSession::create($data);
    }

    public function updateSession(int $id, array $data): ClassSession
    {
        $session = ClassSession::findOrFail($id);
        $data = $this->normalizeStatusTimestamps($data);
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
        $session->update([
            'status' => 'completed',
            'completed_at' => now(),
            'cancelled_at' => null,
        ]);
        return $session;
    }

    public function reopenSession(int $id): ClassSession
    {
        $session = ClassSession::findOrFail($id);
        $session->update([
            'status' => 'scheduled',
            'completed_at' => null,
            'cancelled_at' => null,
        ]);
        return $session;
    }

    public function cancelSession(int $id): ClassSession
    {
        $session = ClassSession::findOrFail($id);
        $session->update([
            'status' => 'cancelled',
            'completed_at' => null,
            'cancelled_at' => now(),
        ]);
        return $session;
    }

    private function normalizeStatusTimestamps(array $data): array
    {
        if (!array_key_exists('status', $data)) {
            return $data;
        }

        if ($data['status'] === 'completed') {
            $data['completed_at'] = $data['completed_at'] ?? now();
            $data['cancelled_at'] = null;
        } elseif ($data['status'] === 'cancelled') {
            $data['completed_at'] = null;
            $data['cancelled_at'] = $data['cancelled_at'] ?? now();
        } elseif ($data['status'] === 'scheduled') {
            $data['completed_at'] = null;
            $data['cancelled_at'] = null;
        }

        return $data;
    }
}
