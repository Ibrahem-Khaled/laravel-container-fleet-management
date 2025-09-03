@extends('layouts.app')

@section('content')
    <div class="container" dir="rtl">
        <div class="d-flex align-items-center justify-content-between mt-4 mb-3">
            <h1 class="h4 mb-0">سجلّ النشاطات (Activity)</h1>

            {{-- زر إعادة الضبط السريع --}}
            <a href="{{ route('logs.activity') }}" class="btn btn-outline-secondary btn-sm">
                إعادة الضبط
            </a>
        </div>

        {{-- فلاتر --}}
        <form method="GET" class="mb-3">
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label class="small text-muted">ID المستخدم</label>
                    <input type="text" name="user_id" value="{{ request('user_id') }}" class="form-control"
                        placeholder="مثال: 12">
                </div>

                <div class="form-group col-md-3">
                    <label class="small text-muted">نوع الموديل (subject)</label>
                    <select name="subject_type" class="form-control">
                        <option value="">— الكل —</option>
                        @foreach ($subjectTypes as $t)
                            <option value="{{ $t }}" @selected(request('subject_type') === $t)>{{ class_basename($t) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-2">
                    <label class="small text-muted">الحدث</label>
                    <select name="event" class="form-control">
                        <option value="">— الكل —</option>
                        @foreach ($events as $e)
                            <option value="{{ $e }}" @selected(request('event') === $e)>{{ $e }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label class="small text-muted">Request ID</label>
                    <input type="text" name="request_id" value="{{ request('request_id') }}" class="form-control"
                        placeholder="إن وُجد">
                </div>

                <div class="form-group col-md-1">
                    <label class="small text-muted">من</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                </div>

                <div class="form-group col-md-1">
                    <label class="small text-muted">إلى</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                </div>
            </div>

            <div class="d-flex">
                <button class="btn btn-primary">تطبيق الفلاتر</button>
            </div>
        </form>

        {{-- الجدول --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="thead-light">
                            <tr class="text-right">
                                <th style="min-width: 140px;">التاريخ</th>
                                <th style="min-width: 160px;">المستخدم</th>
                                <th style="min-width: 110px;">الحدث</th>
                                <th style="min-width: 200px;">الموديل</th>
                                <th>وصف مختصر</th>
                                <th style="width: 1%;">Diff</th>
                                <th style="min-width: 220px;">Meta</th>
                            </tr>
                        </thead>
                        <tbody class="text-right">
                            @forelse($activities as $a)
                                @php
                                    // خريطة ألوان للبادجات حسب الحدث
                                    $event = $a->event ?? $a->description;
                                    $badge =
                                        [
                                            'created' => 'success',
                                            'updated' => 'info',
                                            'deleted' => 'danger',
                                            'restored' => 'warning',
                                        ][$event] ?? 'secondary';

                                    $diffId = 'diff-' . $a->id;
                                    $p = ($a->properties ?? collect())->toArray();
                                    $chg = $a->changes ?? []; // Spatie: old / attributes
                                @endphp

                                <tr>
                                    <td class="align-middle text-monospace">
                                        {{ $a->created_at->format('Y-m-d H:i') }}
                                        <div class="small text-muted">{{ $a->created_at->diffForHumans() }}</div>
                                    </td>

                                    <td class="align-middle">
                                        {{ $a->causer?->name ?? '—' }}
                                        <div class="small text-muted">#{{ $a->causer_id }}</div>
                                    </td>

                                    <td class="align-middle">
                                        <span class="badge badge-{{ $badge }}">{{ $event }}</span>
                                    </td>

                                    <td class="align-middle">
                                        {{ class_basename($a->subject_type) }} <span
                                            class="text-monospace">#{{ $a->subject_id }}</span>
                                    </td>

                                    <td class="align-middle">
                                        {{-- عنوان بشري بسيط --}}
                                        <span class="text-dark">
                                            {{ $a->description ?? '—' }}
                                        </span>
                                    </td>

                                    <td class="align-middle">
                                        @php $hasDiff = !empty(($chg['old'] ?? [])) || !empty(($chg['attributes'] ?? [])); @endphp
                                        @if ($hasDiff)
                                            <button class="btn btn-outline-primary btn-sm" type="button"
                                                data-toggle="collapse" data-target="#{{ $diffId }}"
                                                aria-expanded="false" aria-controls="{{ $diffId }}">
                                                عرض
                                            </button>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td class="align-middle small">
                                        <div class="text-monospace">request_id: {{ $p['request_id'] ?? '—' }}</div>
                                        <div class="text-monospace">ip: {{ $p['ip'] ?? '—' }}</div>
                                        <div class="text-truncate" style="max-width: 240px;"
                                            title="{{ $p['user_agent'] ?? '' }}">
                                            ua: {{ \Illuminate\Support\Str::limit($p['user_agent'] ?? '—', 40) }}
                                        </div>
                                    </td>
                                </tr>

                                {{-- صف قابل للطي لعرض الفروق --}}
                                @if ($hasDiff)
                                    <tr class="bg-light">
                                        <td colspan="7" class="p-0">
                                            <div id="{{ $diffId }}" class="collapse">
                                                <div class="p-3">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <div class="font-weight-bold mb-1">Old</div>
                                                            <pre class="bg-white border rounded p-2 mb-0" style="white-space: pre-wrap; direction: ltr;">
{{ json_encode($chg['old'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                                                        </pre>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <div class="font-weight-bold mb-1">New</div>
                                                            <pre class="bg-white border rounded p-2 mb-0" style="white-space: pre-wrap; direction: ltr;">
{{ json_encode($chg['attributes'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                                                        </pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td class="text-center text-muted py-5" colspan="7">لا توجد سجلات.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ترقيم --}}
                <div class="px-3 py-2">
                    {{ $activities->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
