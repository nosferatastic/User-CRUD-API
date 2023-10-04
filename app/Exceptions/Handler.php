<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

//Exception thrown when model binding fails
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $this->renderable(function (NotFoundHttpException $exception) {
            //If it's a university model binding retrieval that failed, send a relevant error response
            if($exception->getPrevious()?->getModel() == "App\Models\User") {
                return response()->json(['error' => 'This user does not exist.'], 404);
            }
            return response()->json(['error' => 'Invalid request.'], 400);
        });
    }
}
