<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Contracts\LoginViewResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;

class LoginController extends AuthenticatedSessionController
{
    /**
     * Show the login view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Fortify\Contracts\LoginViewResponse
     */
    // public function showLoginForm(Request $request): LoginViewResponse
    // {
    //     return app(LoginViewResponse::class);
    // }

    // /**
    //  * Attempt to authenticate a new session.
    //  *
    //  * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
    //  * @return mixed
    //  */
    // public function attemptLogin(LoginRequest $request)
    // {
    //     return $this->loginPipeline($request)->then(function ($request) {
    //         return app(LoginResponse::class);
    //     });
    // }

    // /**
    //  * Get the authentication pipeline instance.
    //  *
    //  * @param  \Laravel\Fortify\Http\Requests\LoginRequest  $request
    //  * @return \Illuminate\Pipeline\Pipeline
    //  */
    // protected function loginPipeline(LoginRequest $request)
    // {
    //     // Customize the login pipeline if needed
    //     return (new Pipeline(app()))->send($request)->through(array_filter([
    //         config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
    //         config('fortify.lowercase_usernames') ? CanonicalizeUsername::class : null,
    //         Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
    //         AttemptToAuthenticate::class,
    //         PrepareAuthenticatedSession::class,
    //     ]));
    // }

    // /**
    //  * Destroy an authenticated session.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Laravel\Fortify\Contracts\LogoutResponse
    //  */
    // public function logout(Request $request): LogoutResponse
    // {
    //     $this->guard->logout();

    //     if ($request->hasSession()) {
    //         $request->session()->invalidate();
    //         $request->session()->regenerateToken();
    //     }

    //     return app(LogoutResponse::class);
    // }
}