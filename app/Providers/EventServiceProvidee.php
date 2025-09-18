<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // (opsiyonel) e-posta doğrulama kullanıyorsan kalsın
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // GİRİŞTE SEPET BİRLEŞTİRME
        Login::class => [
            \App\Listeners\MergeBasketOnLogin::class,
        ],
    ];

    /**
     * If you prefer auto-discovery, set true; we manuel bağlıyoruz.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
