<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Override;

class CartItem extends Model
{
    protected $guarded =[];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function cartitemable()
    {
        return $this->morphTo();
    }
}
