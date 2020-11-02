<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Response;

class Handler extends ExceptionHandler {

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
    public function report(Throwable $exception) {
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
    public function render($request, Throwable $exception) {
die("cxv");
        $segment = \Illuminate\Support\Facades\Request::segment(1);
        if ($segment == 'api') {

            //var_dump($exception);exit;
            $message = $exception->getMessage();
            if (isset($exception->errorInfo)) {
                $message = explode(";", $exception->errorInfo[2]);
            }
            \Log::error($message);
            // Response::json(array('status' => 'error', 'message' => 'Something was wrong', 'data' => []), 499);

            return Response::json(array('status' => 'error', 'message' => $message, 'data' => []), 499);
        }
        return parent::render($request, $exception);
    }

}
