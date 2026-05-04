<?php

namespace App\Listeners;

use CodeWithDiki\TransactionModule\Facades\TransactionModule as FacadesTransactionModule;
use CodeWithDiki\TransactionModule\TransactionModule;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandlePaymentPaid implements ShouldQueue
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
        $payment = $event->payment;

        FacadesTransactionModule::setPaymentStatus(
            $payment->paymentable, 
            \CodeWithDiki\TransactionModule\Enums\PaymentStatus::PAID, 
            "Payment marked as paid by payment gateway"
        );

        $user = $payment->users()->first();

        if($user) {
            $user->notify(new \App\Notifications\TransactionPaid($payment->paymentable));
        }

    }
}
