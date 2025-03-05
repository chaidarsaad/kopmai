<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use NotificationChannels\Telegram\Telegram;

class IncomingOrder
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $admin = User::where('is_admin', 1)->get();
        $title = 'Ada pesanan baru dari wali santri: ' . $order->user->name;
        $body = 'Untuk santri: ' . $order->nama_santri;

        // $telegram = new Telegram(env('TELEGRAM_BOT_TOKEN'));
        // $telegram->sendMessage([
        //     'chat_id' => env('TELEGRAM_CHAT_ID'),
        //     'text' => $title . "\n" . $body,
        //     'reply_markup' => json_encode([
        //         'inline_keyboard' => [
        //             [
        //                 ['text' => 'Lihat Pesanan', 'url' => route('filament.admin.resources.orders.index')],
        //             ],
        //         ]
        //     ]),
        // ]);

        Notification::make()
            ->title($title)
            ->body($body)
            ->actions([
                Action::make('view')
                    ->label('Lihat')
                    ->url(route('filament.admin.resources.orders.index'))
                    ->button()
                    ->markAsRead(),
            ])
            ->sendToDatabase($admin);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
