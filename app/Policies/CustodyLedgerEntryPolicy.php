<?php

namespace App\Policies;

use App\Models\CustodyLedgerEntry;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CustodyLedgerEntryPolicy
{
    public function delete(User $u, CustodyLedgerEntry $e)
    {
        return $u->hasRole('admin');
    }
}
