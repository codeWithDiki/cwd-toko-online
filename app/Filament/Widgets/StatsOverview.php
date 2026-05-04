<?php

namespace App\Filament\Widgets;

use CodeWithDiki\TransactionModule\Enums\PaymentStatus;
use CodeWithDiki\TransactionModule\Facades\TransactionModule;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Sales', Cache::remember('dashboard_total_sales', now()->addMinutes(10), function () {
                return TransactionModule::getTransactionsCountsByDate(PaymentStatus::PAID);
            })),
            Stat::make('Total Revenue', Cache::remember('dashboard_total_revenue', now()->addMinutes(10), function () {
                return "Rp. " . number_format(TransactionModule::getTransactionSumByDate(PaymentStatus::PAID), 0, ",", ".");
            })), 
             Stat::make('Pending Transactions', Cache::remember('dashboard_pending_transactions', now()->addMinutes(10), function () {
                return TransactionModule::getTransactionsCountsByDate(PaymentStatus::PENDING);
            })),
             Stat::make('Failed Transactions', Cache::remember('dashboard_failed_transactions', now()->addMinutes(10), function () {
                return TransactionModule::getTransactionsCountsByDate(PaymentStatus::FAILED);
            })),
                
        ];
    }
}
