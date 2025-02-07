<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\App;
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
        $segment = $request->segment(1);

        if ($segment == 'api') {

            $errorCode = $exception instanceof AuthenticationException ? 403 : 499;

            $data = [
                'status' => 'error',
                'message' => $exception->getMessage()
            ];

            // if(App::environment('local')) {
            //     $data['exception'] = [
            //         'code' => $exception->getCode(),
            //         'line' => $exception->getLine(),
            //         'file' => $exception->getFile(),
            //         'trace' => $exception->getTrace()
            //     ];
            // }

            return response()->json($data, $errorCode);
        }

        return parent::render($request, $exception);
    }
}
