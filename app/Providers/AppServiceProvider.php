<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            $map = [
                'mail_mailer'       => 'mail.default',
                'mail_host'         => 'mail.mailers.smtp.host',
                'mail_port'         => 'mail.mailers.smtp.port',
                'mail_username'     => 'mail.mailers.smtp.username',
                'mail_password'     => 'mail.mailers.smtp.password',
                'mail_encryption'   => 'mail.mailers.smtp.encryption',
                'mail_from_address' => 'mail.from.address',
                'mail_from_name'    => 'mail.from.name',
            ];

            $settings = \App\Models\Setting::whereIn('key', array_keys($map))
                ->pluck('value', 'key')
                ->toArray();

            foreach ($map as $key => $configKey) {
                if (!empty($settings[$key])) {
                    config([$configKey => $settings[$key]]);
                }
            }
        } catch (\Throwable) {
            // Settings table may not exist before migrations run
        }
    }
}
