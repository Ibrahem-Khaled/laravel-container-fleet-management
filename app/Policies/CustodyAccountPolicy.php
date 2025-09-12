<?php

namespace App\Policies;

use App\Models\CustodyAccount;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CustodyAccountPolicy
{
    public function viewAny(User $u)
    {
        return $u->hasRole('admin') || $u->can('custody.view');
    }
    public function view(User $u, CustodyAccount $a)
    {
        return $this->viewAny($u) || $a->user_id === $u->id;
    }
    public function create(User $u)
    {
        return $u->hasRole('admin') || $u->can('custody.create');
    }
    public function update(User $u, CustodyAccount $a)
    {
        return $u->hasRole('admin') || $u->can('custody.update');
    }
    public function delete(User $u, CustodyAccount $a)
    {
        return $u->hasRole('admin');
    }

    // صلاحيات مخصصة
    public function createEntry(User $u, CustodyAccount $a)
    {
        return $this->update($u, $a);
    }
    public function count(User $u, CustodyAccount $a)
    {
        return $this->update($u, $a);
    }
}
