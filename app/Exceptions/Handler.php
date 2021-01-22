<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Response;

class Handler extends ExceptionHandler
{

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        $segment = \Illuminate\Support\Facades\Request::segment(1);
        if ($segment == 'api') {
            abort_unless(config('app.debug'), 500, 'Internal error occured.');

            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
                'data' => [
                    'file' => $exception->getFile(),
                    'code' => $exception->getCode(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTrace()
                ]
            ], 499);
        }
        
        return parent::render($request, $exception);
    }
}
