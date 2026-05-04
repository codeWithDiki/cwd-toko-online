<?php

// config for VendorName/Skeleton

use App\Listeners\HandleTransactionStatusChanged;
use App\Models\Transaction;
use CodeWithDiki\TransactionModule\Events\TransactionStatusChangedEvent;

return [
    "customer_class" => \CodeWithDiki\TransactionModule\Models\Customer::class,
    "transaction_class" => Transaction::class,
    "transaction_item_class" => \CodeWithDiki\TransactionModule\Models\TransactionItem::class,
    "discount_class" => \CodeWithDiki\TransactionModule\Models\Discount::class,
    "tax" => env("TAX_PERCENTAGE", 0), // in percentage
    "status_after_payment" => \CodeWithDiki\TransactionModule\Enums\TransactionStatus::PROCESSING,
    "log_class" => \CodeWithDiki\TransactionModule\Models\TransactionLog::class,
    "user_class" => \App\Models\User::class,

    // Listeners
    "listeners" => [
        TransactionStatusChangedEvent::class => [
            HandleTransactionStatusChanged::class
        ]
    ]

];
