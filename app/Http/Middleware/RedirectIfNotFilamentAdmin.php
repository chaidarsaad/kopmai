<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Model;

class RedirectIfNotFilamentAdmin extends Middleware
{
    protected function authenticate($request, array $guards)
    {
        $auth = Filament::auth();

        if (!$auth->check()) {
            $this->unauthenticated($request, $guards);
            return;
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

        /** @var Model $user */
        $user = $auth->user();

        $panel = Filament::getCurrentPanel();

        if ($user->roles->isEmpty()) {
            session()->flash('alert_message', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
            session()->flash('alert_type', 'error'); // success, error, warning, info
            return redirect(route('home'))->send();
        }

        abort_if(
            $user instanceof FilamentUser && ! $user->canAccessPanel($panel),
            403
        );
    }

    protected function redirectTo($request): ?string
    {
        return Filament::getLoginUrl();
    }
}
