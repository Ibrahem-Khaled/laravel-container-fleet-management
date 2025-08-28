<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Models\Car;
use App\Models\User;
use Illuminate\Http\Request;

class CarController extends Controller
{
    /**
     * عرض قائمة السيارات مع الإحصائيات والفلترة والبحث
     */
    public function index(Request $request)
    {
        // استعلام أساسي لجلب السيارات
        $query = Car::with('driver'); // Eager load the driver relationship

        // فلترة حسب النوع (من التبويبات)
        $selectedType = $request->get('type', 'all');
        if ($selectedType && $selectedType !== 'all') {
            $query->where('type', $selectedType);
        }

        // بحث
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('type_car', 'like', "%{$search}%")
                    ->orWhere('model_car', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('number', 'like', "%{$search}%")
                    ->orWhereHas('driver', function ($driverQuery) use ($search) {
                        $driverQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // جلب البيانات مع الترقيم
        $cars = $query->latest()->paginate(10);

        // حساب الإحصائيات
        $stats = [
            'total' => Car::count(),
            'transfer' => Car::where('type', 'transfer')->count(),
            'private' => Car::where('type', 'private')->count(),
            'with_driver' => Car::whereNotNull('driver_id')->count(),
        ];

        // جلب السائقين المتاحين (الذين لم يتم ربطهم بسيارة بعد)
        $availableDrivers = User::WithoutRoles(['client', 'admin', 'clearance_office'])
            ->whereDoesntHave('car')->get();

        // أنواع السيارات للتبويبات
        $types = ['transfer', 'private'];

        return view('dashboard.cars.index', compact('cars', 'stats', 'types', 'selectedType', 'availableDrivers'));
    }

    /**
     * تخزين سيارة جديدة في قاعدة البيانات
     */
    public function store(StoreCarRequest $request)
    {
        Car::create($request->validated());

        return redirect()->route('cars.index')->with('success', 'تمت إضافة السيارة بنجاح.');
    }

    /**
     * تحديث بيانات سيارة موجودة
     */
    public function update(UpdateCarRequest $request, Car $car)
    {
        $car->update($request->validated());

        return redirect()->route('cars.index')->with('success', 'تم تحديث بيانات السيارة بنجاح.');
    }

    /**
     * حذف سيارة (حذف ناعم)
     */
    public function destroy(Car $car)
    {
        $car->delete();

        return redirect()->route('cars.index')->with('success', 'تم حذف السيارة بنجاح.');
    }
}
