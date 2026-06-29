@push('styles')
<style>
    .guide-hero {
        border-radius: 22px;
        padding: 22px;
        background: linear-gradient(135deg, #2563eb, #06b6d4);
        color: #fff;
        box-shadow: 0 18px 36px rgba(37, 99, 235, .18);
        margin-bottom: 16px;
    }

    .guide-hero h1 {
        font-size: 1.55rem;
        font-weight: 900;
        margin: 8px 0 6px;
    }

    .guide-hero p {
        margin: 0;
        font-weight: 700;
        opacity: .92;
        max-width: 760px;
    }

    .guide-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .18);
        font-size: .78rem;
        font-weight: 900;
    }

    .guide-shell {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #e7ecff;
        box-shadow: 0 12px 28px rgba(15, 23, 42, .06);
        overflow: hidden;
    }

    .guide-tabs {
        display: flex;
        gap: 8px;
        padding: 12px;
        background: #f8fbff;
        border-bottom: 1px solid #e7ecff;
    }

    .guide-tabs .nav-link {
        border: 0;
        border-radius: 12px;
        color: #475569;
        font-weight: 900;
        padding: 10px 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .guide-tabs .nav-link.active {
        background: #2563eb;
        color: #fff;
    }

    .guide-content {
        padding: 18px;
    }

    .guide-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .guide-card {
        border: 1px solid #e9edff;
        border-radius: 16px;
        padding: 16px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        min-height: 100%;
    }

    .guide-card h3 {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1rem;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 12px;
    }

    .guide-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #dbeafe;
        color: #1d4ed8;
        flex-shrink: 0;
    }

    .guide-steps {
        margin: 0;
        padding-left: 1.2rem;
        color: #334155;
        font-weight: 650;
        line-height: 1.7;
    }

    .guide-note {
        border-radius: 14px;
        padding: 13px 15px;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        color: #9a3412;
        font-weight: 750;
        margin-top: 14px;
    }

    @media (max-width: 768px) {
        .guide-hero {
            border-radius: 18px;
            padding: 18px;
        }

        .guide-grid {
            grid-template-columns: 1fr;
        }

        .guide-tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .guide-tabs .nav-link {
            justify-content: center;
            padding: 10px 8px;
            font-size: .86rem;
        }

        .guide-content {
            padding: 14px;
        }
    }
</style>
@endpush

@php
    $studentCards = [
        ['icon' => 'bi-person-check', 'title' => 'enter_area_title', 'steps' => 'enter_area_steps'],
        ['icon' => 'bi-journal-bookmark', 'title' => 'session_title', 'steps' => 'session_steps'],
        ['icon' => 'bi-pencil-square', 'title' => 'practice_title', 'steps' => 'practice_steps'],
        ['icon' => 'bi-clipboard-check', 'title' => 'result_title', 'steps' => 'result_steps'],
    ];

    $adminCards = [
        ['icon' => 'bi-people-fill', 'title' => 'class_title', 'steps' => 'class_steps'],
        ['icon' => 'bi-calendar-event', 'title' => 'session_title', 'steps' => 'session_steps'],
        ['icon' => 'bi-collection-fill', 'title' => 'bank_title', 'steps' => 'bank_steps'],
        ['icon' => 'bi-file-earmark-spreadsheet', 'title' => 'import_title', 'steps' => 'import_steps'],
        ['icon' => 'bi-clipboard2-check-fill', 'title' => 'assign_title', 'steps' => 'assign_steps'],
        ['icon' => 'bi-folder2-open', 'title' => 'ckfinder_title', 'steps' => 'ckfinder_steps'],
    ];
@endphp

<section class="guide-hero">
    <span class="guide-badge"><i class="bi bi-compass"></i>{{ __('ui.guide.badge') }}</span>
    <h1>{{ __('ui.guide.heading') }}</h1>
    <p>{{ __('ui.guide.intro') }}</p>
</section>

<section class="guide-shell">
    <ul class="nav guide-tabs" id="guideTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="student-guide-tab" data-bs-toggle="tab" data-bs-target="#student-guide" type="button" role="tab">
                <i class="bi bi-mortarboard-fill"></i>{{ __('ui.guide.student_tab') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="admin-guide-tab" data-bs-toggle="tab" data-bs-target="#admin-guide" type="button" role="tab">
                <i class="bi bi-shield-check"></i>{{ __('ui.guide.admin_tab') }}
            </button>
        </li>
    </ul>

    <div class="tab-content guide-content">
        <div class="tab-pane fade show active" id="student-guide" role="tabpanel" aria-labelledby="student-guide-tab">
            <div class="guide-grid">
                @foreach($studentCards as $card)
                    <article class="guide-card">
                        <h3><span class="guide-icon"><i class="bi {{ $card['icon'] }}"></i></span>{{ __('ui.guide.student.' . $card['title']) }}</h3>
                        <ol class="guide-steps">
                            @foreach(__('ui.guide.student.' . $card['steps']) as $step)
                                <li>{{ $step }}</li>
                            @endforeach
                        </ol>
                    </article>
                @endforeach
            </div>

            <div class="guide-note">{{ __('ui.guide.student.note') }}</div>
        </div>

        <div class="tab-pane fade" id="admin-guide" role="tabpanel" aria-labelledby="admin-guide-tab">
            <div class="guide-grid">
                @foreach($adminCards as $card)
                    <article class="guide-card">
                        <h3><span class="guide-icon"><i class="bi {{ $card['icon'] }}"></i></span>{{ __('ui.guide.admin_guide.' . $card['title']) }}</h3>
                        <ol class="guide-steps">
                            @foreach(__('ui.guide.admin_guide.' . $card['steps']) as $step)
                                <li>{{ $step }}</li>
                            @endforeach
                        </ol>
                    </article>
                @endforeach
            </div>

            <div class="guide-note">{{ __('ui.guide.admin_guide.note') }}</div>
        </div>
    </div>
</section>
