<?php

namespace App\Policies;

use App\Models\CashCount;
use App\Models\User;
use App\Models\CustodyAccount;
use Illuminate\Auth\Access\Response;

class CashCountPolicy
{
    public function viewAny(User $u)
    {
        return $u->hasRole('admin') || $u->can('custody.view');
    }
    public function create(User $u, CustodyAccount $a)
    {
        return $u->hasRole('admin') || $u->can('custody.update');
    }
    public function post(User $u, CashCount $c)
    {
        return $u->hasRole('admin') || $u->can('custody.update');
    }
}
