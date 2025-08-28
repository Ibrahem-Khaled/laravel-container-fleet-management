<?php

namespace App\Traits;

use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;

trait FiltersByRole
{
    public function scopeWithRoleModel(Builder $query, Role $role): Builder
    {
        if (method_exists($query->getModel()->newQuery(), 'whereBelongsTo')) {
            return $query->whereBelongsTo($role, 'role');
        }

        return $query->whereHas('role', fn(Builder $q) => $q->whereKey($role->getKey()));
    }

    public function scopeWithRoles(Builder $query, string|array $names): Builder
    {
        $names = (array) $names;
        return $query->whereHas('role', fn(Builder $q) => $q->whereIn('name', $names));
    }

    public function scopeWithoutRoles(Builder $query, string|array $names): Builder
    {
        $names = (array) $names;
        return $query->whereDoesntHave('role', fn(Builder $q) => $q->whereIn('name', $names));
    }

    public function scopeWithRoleNames(Builder $query, string|array $names): Builder
    {
        $names = (array) $names;

        return $query
            // لاحظ: شِلّ الـ type-hint هنا لأن لاراڤيل قد يمرّر Relation (BelongsTo)
            ->with(['role' => function ($q) use ($names) {
                $q->whereIn('name', $names);
            }])
            // هنا النوع آمن أنه يكون Illuminate\Database\Eloquent\Builder
            ->whereHas('role', function (Builder $q) use ($names) {
                $q->whereIn('name', $names);
            });
    }
}
