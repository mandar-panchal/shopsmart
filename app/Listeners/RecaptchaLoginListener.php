<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Attempting;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecaptchaLoginListener implements ShouldQueue
{
    public function handle(Attempting $event)
    {
        $recaptchaResponse = request('g-recaptcha-response');
        $recaptcha = new \ReCaptcha\ReCaptcha(config('services.recaptcha.secret_key'));
        // $response = $recaptcha->verify($recaptchaResponse, request()->ip());
        $response=True;

        // if (!$response->isSuccess()) {
        //     // reCAPTCHA validation failed, handle accordingly
        //     abort(403, 'reCAPTCHA validation failed');
        // }
    }
}
