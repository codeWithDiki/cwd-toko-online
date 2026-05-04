<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use \CodeWithDiki\TransactionModule\Enums\TransactionStatus;

class HandleTransactionStatusChanged implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $transaction = $event->transaction;

        match($transaction->status) {
            TransactionStatus::ONDELIVERY => $transaction->users()->first()?->notify(new \App\Notifications\TransactionOnDelivery($transaction)),
            TransactionStatus::RETURNED => $transaction->users()->first()?->notify(new \App\Notifications\TransactionReturned($transaction)),
            TransactionStatus::REFUNDED => $transaction->users()->first()?->notify(new \App\Notifications\TransactionRefunded($transaction)),
            TransactionStatus::FAILED => $transaction->users()->first()?->notify(new \App\Notifications\TransactionStatusFailed($transaction)),
            TransactionStatus::COMPLETED => $transaction->users()->first()?->notify(new \App\Notifications\TransactionCompleted($transaction)),
            TransactionStatus::CANCELLED => $transaction->users()->first()?->notify(new \App\Notifications\TransactionCanceled($transaction)),
            TransactionStatus::DELIVERED => $transaction->users()->first()?->notify(new \App\Notifications\TransactionDelivered($transaction)),
            default => null
        };


    }
}
