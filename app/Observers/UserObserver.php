<?php

namespace App\Observers;

use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use NotificationChannels\Telegram\Telegram;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $admin = User::where('is_admin', 1)->get();
        $title = 'Ada pengguna baru dengan nama: ' . $user->name;
        $body = 'email: ' . $user->email;
        Notification::make()
            ->title($title)
            ->body($body)
            ->actions([
                Action::make('view')
                    ->label('Lihat')
                    ->url(fn() => route('filament.pengelola.resources.pengguna.index'))
                    ->button()
                    ->markAsRead(),
            ])
            ->sendToDatabase($admin);

        // $telegram = new Telegram(env('TELEGRAM_BOT_TOKEN'));
        // $telegram->sendMessage([
        //     'chat_id' => env('TELEGRAM_CHAT_ID'),
        //     'text' => $title . "\n" . $body,
        // ]);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
