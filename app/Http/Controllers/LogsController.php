<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;
use OwenIt\Auditing\Models\Audit;
use App\Support\LogHumanizer;

class LogsController extends Controller
{
    public function activity(Request $request)
    {
        $query = Activity::query()
            ->with(['causer:id,name,email', 'subject']); // تفادي N+1

        // فلاتر
        $query->when(
            $request->filled('user_id'),
            fn($q) => $q->where('causer_id', $request->integer('user_id'))
        );
        $query->when(
            $request->filled('subject_type'),
            fn($q) => $q->where('subject_type', $request->string('subject_type'))
        );
        $query->when(
            $request->filled('event'),
            fn($q) => $q->where('event', $request->string('event'))
        );
        // البحث داخل JSON (properties->request_id/ip) حسب دعم قاعدة البيانات
        $query->when(
            $request->filled('request_id'),
            fn($q) => $q->where('properties->request_id', $request->string('request_id'))
        );

        if ($request->filled(['from', 'to'])) {
            $from = Carbon::parse($request->input('from'))->startOfDay();
            $to   = Carbon::parse($request->input('to'))->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }

        $activities = $query->latest()->paginate(20)->withQueryString();

        // بطاقات بشرية جاهزة للعرض
        $cards = $activities->getCollection()->map(function (Activity $a) {
            $who   = $a->causer?->name;
            $sub   = LogHumanizer::subjectLabel($a->subject_type, $a->subject_id);
            $title = LogHumanizer::title($who, $a->event ?? $a->description, $sub);

            $chg   = $a->changes ?? []; // Spatie: ['old'=>..., 'attributes'=>...]
            $lines = LogHumanizer::lines($chg['old'] ?? [], $chg['attributes'] ?? []);

            return [
                'title' => $title,
                'when'  => $a->created_at->diffForHumans(),
                'meta'  => [
                    'request_id' => $a->properties['request_id'] ?? null,
                    'ip'         => $a->properties['ip'] ?? null,
                    'user_agent' => $a->properties['user_agent'] ?? null,
                ],
                'lines' => $lines,
            ];
        });

        // خيارات الفلاتر (قوائم القيم)
        $subjectTypes = Activity::query()->select('subject_type')->distinct()->pluck('subject_type')->filter();
        $events       = Activity::query()->select('event')->distinct()->pluck('event')->filter();

        return view('dashboard.logs.activity_human', [
            'activities'   => $activities,
            'cards'        => $cards,
            'subjectTypes' => $subjectTypes,
            'events'       => $events,
        ]);
    }

    public function audits(Request $request)
    {
        $query = Audit::query()->with(['auditable']);

        $query->when(
            $request->filled('user_id'),
            fn($q) => $q->where('user_id', $request->integer('user_id')) // حسب إعداد الـ resolver
        );
        $query->when(
            $request->filled('auditable_type'),
            fn($q) => $q->where('auditable_type', $request->string('auditable_type'))
        );
        $query->when(
            $request->filled('event'),
            fn($q) => $q->where('event', $request->string('event'))
        );

        if ($request->filled(['from', 'to'])) {
            $from = Carbon::parse($request->input('from'))->startOfDay();
            $to   = Carbon::parse($request->input('to'))->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }

        $audits = $query->latest()->paginate(20)->withQueryString();

        $cards = $audits->getCollection()->map(function (Audit $au) {
            $who   = optional($au->user)->name ?? ($au->user_id ? "#{$au->user_id}" : null);
            $sub   = LogHumanizer::subjectLabel($au->auditable_type, $au->auditable_id);
            $title = LogHumanizer::title($who, $au->event, $sub);

            $lines = LogHumanizer::lines($au->old_values ?? [], $au->new_values ?? []);

            return [
                'title' => $title,
                'when'  => $au->created_at->diffForHumans(),
                'meta'  => [
                    'ip'         => $au->ip_address ?? null,
                    'user_agent' => $au->user_agent ?? null,
                ],
                'lines' => $lines,
            ];
        });

        $auditableTypes = Audit::query()->select('auditable_type')->distinct()->pluck('auditable_type')->filter();
        $events         = Audit::query()->select('event')->distinct()->pluck('event')->filter();

        return view('dashboard.logs.audits_human', [
            'audits'         => $audits,
            'cards'          => $cards,
            'auditableTypes' => $auditableTypes,
            'events'         => $events,
        ]);
    }
}
