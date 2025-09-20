<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficeTaxHistory extends Model
{
    protected $fillable = [
        'office_id',
        'tax_enabled',
        'effective_from',
        'effective_to',
        'tax_rate',
        'notes',
        'changed_by',
    ];

    protected $casts = [
        'tax_enabled' => 'boolean',
        'effective_from' => 'datetime',
        'effective_to' => 'datetime',
        'tax_rate' => 'decimal:2',
    ];

    /**
     * العلاقة مع المكتب الجمركي
     */
    public function office(): BelongsTo
    {
        return $this->belongsTo(User::class, 'office_id');
    }

    /**
     * العلاقة مع المستخدم الذي قام بالتغيير
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * الحصول على حالة الضرائب في تاريخ معين
     */
    public static function getTaxStatusForDate($officeId, $date)
    {
        return self::where('office_id', $officeId)
            ->where('effective_from', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', $date);
            })
            ->orderBy('effective_from', 'desc')
            ->first();
    }

    /**
     * الحصول على جميع فترات الضرائب لمكتب معين
     */
    public static function getTaxPeriodsForOffice($officeId)
    {
        return self::where('office_id', $officeId)
            ->orderBy('effective_from', 'desc')
            ->get();
    }

    /**
     * إنهاء الفترة الحالية وبدء فترة جديدة
     */
    public static function createNewTaxPeriod($officeId, $taxEnabled, $effectiveFrom, $taxRate = 15.00, $notes = null, $changedBy = null)
    {
        // إنهاء الفترة الحالية
        self::where('office_id', $officeId)
            ->whereNull('effective_to')
            ->update(['effective_to' => $effectiveFrom]);

        // إنشاء فترة جديدة
        return self::create([
            'office_id' => $officeId,
            'tax_enabled' => $taxEnabled,
            'effective_from' => $effectiveFrom,
            'effective_to' => null,
            'tax_rate' => $taxRate,
            'notes' => $notes,
            'changed_by' => $changedBy ?? auth()->id(),
        ]);
    }
}
