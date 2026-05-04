<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Payment extends \CodeWithDiki\PaymentModule\Models\Payment
{
    // You can add custom methods or properties here if needed

    public function users() : BelongsToMany
    {
        return $this->belongsToMany(config('auth.providers.users.model'), 'payment_user', 'payment_id', 'user_id')->withTimestamps();
    }

}