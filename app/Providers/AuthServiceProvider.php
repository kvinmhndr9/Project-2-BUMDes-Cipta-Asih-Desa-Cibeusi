<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verifikasi Email - SI-ASIH BUMDes Cipta Asih')
                ->greeting('Sampurasun, ' . $notifiable->name . '!')
                ->line('Terima kasih telah mendaftar di sistem e-tiket BUMDes Cipta Asih (SI-ASIH).')
                ->line('Silakan klik tombol di bawah ini untuk memverifikasi alamat email dan mengaktifkan akun Anda.')
                ->action('Verifikasi Email Saya', $url)
                ->line('Jika Anda tidak merasa membuat akun ini, silakan abaikan email ini.');
        });
    }
}
