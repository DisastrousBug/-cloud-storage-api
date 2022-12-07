<?php

namespace App\Providers;

use App\Models\File;
use App\Models\User;
use App\Observers\UserObserver;
use App\Observers\FileObserver;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//        ResetPassword::createUrlUsing(
//            function ($notifiable, $token) {
//                return config('app.client_url')."?type=reset_password&token={$token}&email={$notifiable->getEmailForPasswordReset()}";
//
//            }
//        );
//
//        VerifyEmail::createUrlUsing(
//            function ($notifiable) {
//                $email = $notifiable->getEmailForVerification();
//                $hash = Hash::make($email);
//
//                return config('app.url')."/api/v1/email/verify/{$email}?hash={$hash}";
//            }
//        );

        User::observe(UserObserver::class);
        File::observe(FileObserver::class);
    }
}
