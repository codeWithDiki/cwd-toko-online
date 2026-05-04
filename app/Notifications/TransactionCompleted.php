<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionCompleted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public \CodeWithDiki\TransactionModule\Models\Transaction $transaction
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
            ->greeting("Terimakasih Sudah berbelanja Ditoko Kami!")
            ->line('Jangan ragu untuk menghubungi kami jika ada pertanyaan seputar transaksi dengan nomor transaksi: ' . $this->transaction->trx_id)
            ->action('Lihat Detail', route('transaction.view', $this->transaction->trx_id))
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
