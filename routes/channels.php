<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('payment-created', function (User $user) {
    if($user){
        return true;
    }

    return false;
});

Broadcast::channel('payment-paid', function (User $user) {
    if($user){
        return true;
    }

    return false;
});

Broadcast::channel('payment-failed', function (User $user) {
    if($user){
        return true;
    }

    return false;
});

Broadcast::channel('payment-gateway-processed', function (User $user) {
    if($user){
        return true;
    }
    return false;
});
