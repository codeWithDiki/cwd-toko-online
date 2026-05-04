<?php

namespace App\Notifications;

use CodeWithDiki\TransactionModule\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionPaid extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Transaction $transaction
    )
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting("Transaksi Sudah Dibayar!")
            ->line('Terimakasih sudah melakukan pembayaran untuk transaksi dengan nomor transaksi: ' . $this->transaction->trx_id)
            ->line('Total pembayaran: ' . number_format($this->transaction->total_amount, 0, ',', '.'))
            ->line('Status pembayaran: ' . $this->transaction->payment_status->value)
            ->line('Status transaksi: ' . $this->transaction->status->value)
            ->line('Jika ada pertanyaan, silahkan hubungi kami.')
            ->action('Lihat Detail Transaksi', route('transaction.view', $this->transaction->trx_id))
            ->line('Terimakasih sudah berbelanja di toko kami!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
