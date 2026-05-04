<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Transaction extends \CodeWithDiki\TransactionModule\Models\Transaction
{
    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class, "transaction_user", "transaction_id", "user_id");
    }
}