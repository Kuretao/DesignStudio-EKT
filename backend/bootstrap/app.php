<?php

use App\Support\AdminErrorMessage;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $exception, Request $request) {
            $adminPrefix = trim((string) config('moonshine.prefix', 'admin'), '/');
            $isAdminPath = $request->path() === $adminPrefix
                || str_starts_with($request->path(), $adminPrefix . '/');
            $isFormRequest = in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true);

            if (! $isAdminPath || (! $isFormRequest && ! $request->expectsJson() && ! $request->ajax())) {
                return null;
            }

            $message = AdminErrorMessage::fromThrowable($exception);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $message,
                    'errors' => [
                        'cms' => [$message],
                    ],
                ], 422);
            }

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['cms' => $message]);
        });
    })->create();
