<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\PartnerCapitalMovement;
use App\Models\ProfitRun;
use App\Models\User;
use App\Services\MonthlyNetProfitService;
use App\Services\ProfitDistributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PartnerProfitController extends Controller
{
    /*========================
     | 1) إدارة الشركاء
     *========================*/

    // قائمة الشركاء + بحث + قائمة المستخدمين المؤهلين للإضافة (role=partner ولم يُضف كشريك)
    public function index(Request $request)
    {
        $q = Partner::query()
            ->with(['user.role'])
            ->withCount('movements')
            ->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $s = $request->string('search')->toString();
            $q->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%{$s}%")
                    ->orWhereHas('user', function ($uq) use ($s) {
                        $uq->where('name', 'like', "%{$s}%")
                            ->orWhere('email', 'like', "%{$s}%");
                    });
            });
        }

        $partners = $q->paginate(12);

        // 🟢 حساب إجمالي رؤوس الأموال لجميع الشركاء
        $totalCapital = Partner::all()->sum(function ($partner) {
            return $partner->currentBalance();
        });

        // إضافة النسبة لكل شريك
        foreach ($partners as $partner) {
            $partner->percentage = $totalCapital > 0
                ? ($partner->currentBalance() / $totalCapital) * 100
                : 0;
        }

        $eligibleUsers = User::query()
            ->withRoles('partner')
            ->whereDoesntHave('partner')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('dashboard.company.partners', compact('partners', 'eligibleUsers', 'totalCapital'));
    }

    // إضافة شريك
    public function store(Request $request)
    {
        // فاليديشن أولي
        $data = $request->validate([
            'name'      => 'required|string|max:190',
            'user_id'   => [
                'required',
                Rule::exists('users', 'id'),            // موجود أساسًا
                Rule::unique('partners', 'user_id'),    // مش مكرر في partners
            ],
            // 'is_active' => 'nullable|boolean',
        ]);

        // تحقق إضافي: لازم المستخدم المختار رولُه partner عبر العلاقة
        $isPartnerRole = User::query()
            ->withRoles('partner')
            ->whereKey($data['user_id'])
            ->exists();

        if (!$isPartnerRole) {
            return back()->withErrors(['user_id' => 'المستخدم المختار ليس له رول partner.'])->withInput();
        }

        $data['is_active'] = (bool)($data['is_active'] ?? true);
        Partner::create($data);

        return back()->with('success', 'تم إضافة الشريك بنجاح.');
    }

    // تعديل شريك
    public function update(Request $request, Partner $partner)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:190',
            'user_id'   => [
                'required',
                Rule::exists('users', 'id'),
                Rule::unique('partners', 'user_id')->ignore($partner->id),
            ],
            'is_active' => 'nullable|boolean',
        ]);

        // برضه نتحقق من رول المستخدم
        $isPartnerRole = \App\Models\User::query()
            ->withRoles('partner')
            ->whereKey($data['user_id'])
            ->exists();

        if (!$isPartnerRole) {
            return back()->withErrors(['user_id' => 'المستخدم المختار ليس له رول partner.'])->withInput();
        }

        $partner->update($data);
        return back()->with('success', 'تم تحديث بيانات الشريك.');
    }

    // حذف شريك
    public function destroy(Partner $partner)
    {
        $partner->delete();
        return back()->with('success', 'تم حذف الشريك.');
    }

    // إضافة نفسي كشريك (لو role=partner ولم يُضف من قبل)
    public function attachMe()
    {
        $user = auth()->user();

        // لازم رول partner عبر العلاقة
        if (!$user || !$user->role || $user->role->name !== 'partner') {
            return back()->with('error', 'حسابك ليس له دور partner.');
        }

        // منع التكرار
        if ($user->partner) {
            return back()->with('info', 'أنت مُسجَّل بالفعل كشريك.');
        }

        Partner::create([
            'user_id'   => $user->id,
            'name'      => $user->name ?? 'أنا',
            'is_active' => true,
        ]);

        return back()->with('success', 'تم إضافة حسابك كشريك.');
    }

    /*========================
     | 2) حركات رأس المال
     *========================*/

    public function movementsIndex(Partner $partner)
    {
        $movements = $partner->movements()
            ->orderBy('occurred_at', 'desc')
            ->paginate(15);

        $currentBalance = $partner->currentBalance();

        return view('dashboard.company.movements', compact('partner', 'movements', 'currentBalance'));
    }

    public function movementsStore(Request $request, Partner $partner)
    {
        $data = $request->validate([
            'type'        => 'required|in:deposit,withdrawal',
            'amount'      => 'required|numeric|min:0.01',
            'occurred_at' => 'required|date',
            'notes'       => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($partner, $data) {
            PartnerCapitalMovement::create([
                'partner_id'  => $partner->id,
                'type'        => $data['type'],
                'amount'      => $data['amount'],
                'occurred_at' => $data['occurred_at'],
                'notes'       => $data['notes'] ?? null,
            ]);
        });

        return back()->with('success', 'تم تسجيل الحركة بنجاح.');
    }

    public function movementsDestroy(Partner $partner, PartnerCapitalMovement $movement)
    {
        abort_unless($movement->partner_id === $partner->id, 403);
        $movement->delete();

        return back()->with('success', 'تم حذف الحركة.');
    }

    /*========================
     | 3) توزيع الأرباح الشهري
     *========================*/

    public function profitIndex(Request $request, MonthlyNetProfitService $netSrv)
    {
        $year  = (int)($request->get('year')  ?? now()->year);
        $month = (int)($request->get('month') ?? now()->month);

        $net = $netSrv->netFor($year, $month);

        $run = ProfitRun::with(['allocations.partner'])
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        $allocationsWithPercent = collect();
        $prospectiveShares = collect();
        $totalCapital = 0;

        if ($run && $run->allocations->count()) {
            // ✅ لدينا توزيع منفذ: احسب نسبة كل شريك من إجمالي المبالغ الموزعة
            $totalDistributed = $run->allocations->sum('amount');

            $allocationsWithPercent = $run->allocations->map(function ($alloc) use ($totalDistributed) {
                $alloc->percent = $totalDistributed > 0
                    ? round(($alloc->amount / $totalDistributed) * 100, 2)
                    : 0;
                return $alloc;
            });
        } else {
            // ✅ لا يوجد توزيع حتى الآن: احسب نسب تمهيدية حسب الرصيد الحالي (رأس المال)
            $partners = \App\Models\Partner::query()
                ->where('is_active', true)
                ->get();

            // اجمالي رؤوس الأموال الحالية
            $totalCapital = $partners->sum(function ($p) {
                return $p->currentBalance();
            });

            $prospectiveShares = $partners->map(function ($p) use ($totalCapital) {
                $balance = $p->currentBalance();
                return (object)[
                    'partner'  => $p,
                    'balance'  => $balance,
                    'percent'  => $totalCapital > 0 ? round(($balance / $totalCapital) * 100, 2) : 0,
                ];
            })->sortByDesc('balance')->values();
        }

        return view(
            'dashboard.company.profit',
            compact('year', 'month', 'net', 'run', 'allocationsWithPercent', 'prospectiveShares', 'totalCapital')
        );
    }


    public function profitRun(Request $request, MonthlyNetProfitService $netSrv, ProfitDistributionService $distSrv)
    {
        $data = $request->validate([
            'year'  => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $net = $netSrv->netFor($data['year'], $data['month']);
        $distSrv->runForMonth($data['year'], $data['month'], $net);

        return redirect()
            ->route('partners.profit.index', ['year' => $data['year'], 'month' => $data['month']])
            ->with('success', 'تم تنفيذ توزيع الأرباح لهذا الشهر.');
    }
}
