<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {   
        //var_dump($exception);

        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'error' => 'Entry for '.str_replace('App\\', '', $exception->getModel()).' not found'
            ], 404);
        } elseif ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'error' => 'Ressource not found',
                'exception' => 'Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException'
            ], 404);
        }



        return parent::render($request, $exception);

        /*if (env('APP_DEBUG')) {
            return parent::render($request, $e);
        }
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        if ($e instanceof HttpResponseException) {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        } elseif ($e instanceof MethodNotAllowedHttpException) {
            $status = Response::HTTP_METHOD_NOT_ALLOWED;
            $e = new MethodNotAllowedHttpException([], 'HTTP_METHOD_NOT_ALLOWED', $e);
        } elseif ($e instanceof NotFoundHttpException) {
            $status = Response::HTTP_NOT_FOUND;
            $e = new NotFoundHttpException('HTTP_NOT_FOUND', $e);
        } elseif ($e instanceof AuthorizationException) {
            $status = Response::HTTP_FORBIDDEN;
            $e = new AuthorizationException('HTTP_FORBIDDEN', $status);
        } elseif ($e instanceof \Dotenv\Exception\ValidationException && $e->getResponse()) {
            $status = Response::HTTP_BAD_REQUEST;
            $e = new \Dotenv\Exception\ValidationException('HTTP_BAD_REQUEST', $status, $e);
        } elseif ($e) {
            $e = new HttpException($status, 'HTTP_INTERNAL_SERVER_ERROR');
        }
        return response()->json([
          'success' => false,
          'status' => $status,
          'message' => $e->getMessage()
        ], $status); */

    }
}
