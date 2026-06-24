<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTestSessionRequest;
use App\Models\SchoolClass;
use App\Models\SchoolTest;
use App\Models\TestSession;
use Illuminate\Http\Request;

class TestSessionController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::with(['testSessions' => function ($q) {
            $q->with(['test.questions', 'submissions'])->latest('starts_at');
        }])->orderBy('name')->get();

        return view('admin.test-sessions.index', compact('classes'));
    }

    public function create(Request $request)
    {
        $tests = SchoolTest::with('schoolClass')
            ->withCount('questions')
            ->orderBy('title')
            ->get();

        $classes = SchoolClass::where('status', 'active')->orderBy('name')->get();
        $selectedTest = $request->query('test_id') ? SchoolTest::find($request->query('test_id')) : null;

        return view('admin.test-sessions.create', compact('tests', 'classes', 'selectedTest'));
    }

    public function store(StoreTestSessionRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $session = TestSession::create($data);

        return redirect()->route('admin.test-sessions.index')
            ->with('success', 'Tạo phiên kiểm tra thành công: ' . $session->display_title);
    }

    public function open(int $id)
    {
        $session = TestSession::with('test')->withCount('submissions')->findOrFail($id);

        abort_if($session->test->questions()->count() === 0, 422, 'Đề thi cần có ít nhất 1 câu hỏi trước khi mở phiên.');
        abort_if($session->status === 'closed', 422, 'Không thể mở lại phiên đã đóng.');

        $session->update(['status' => 'open']);

        return back()->with('success', 'Đã mở phiên kiểm tra.');
    }

    public function close(int $id)
    {
        $session = TestSession::findOrFail($id);
        $session->update(['status' => 'closed']);

        return back()->with('success', 'Đã đóng phiên kiểm tra.');
    }

    public function destroy(int $id)
    {
        $session = TestSession::withCount('submissions')->findOrFail($id);

        abort_if($session->submissions_count > 0, 422, 'Không thể xoá phiên đã có bài nộp.');

        $session->delete();

        return back()->with('success', 'Đã xoá phiên kiểm tra.');
    }
}
