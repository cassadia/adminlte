<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, \Throwable $exception)
    {
        // if ($exception instanceof AccessDeniedHttpException) {
        //     // Redirect ke halaman fallback jika terjadi error 403
        //     // return redirect()->route('fallback.dashboard');
        //     return redirect()->route('public.profile.show');
        // }
        if ($exception instanceof AccessDeniedHttpException && $request->route()->getName() !== 'public.profile.show') {
            return redirect()->route('public.profile.show');
        }

        return parent::render($request, $exception);
    }
}
