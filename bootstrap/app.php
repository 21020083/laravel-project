<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
        $exceptions->render(function (Request $request, Throwable $e){
            if ($request->is('api/*')) {
                if ($e instanceof PostTooLargeException) {
                    $message = "Size of attached file should be less " . ini_get("upload_max_filesize") . "B";
                    return $this->sendError($message);
                }

                if ($e instanceof AuthenticationException) {
                    $message = 'Unauthenticated or token expired, please login';
                    return $this->sendError($message, null, Response::HTTP_UNAUTHORIZED);
                }

                if ($e instanceof ThrottleRequestsException) {
                    $message = 'Too many requests, please slow down';
                    return $this->sendError($message, null, Response::HTTP_TOO_MANY_REQUESTS);
                }

                if ($e instanceof ModelNotFoundException) {
                    $message = 'Entry for ' . str_replace('App\\', '', $e->getModel()) . ' not found';
                    return $this->sendError($message, null, Response::HTTP_NOT_FOUND);
                }

                if ($e instanceof QueryException) {
                    $message = 'There was issue with the query' . ', errors:' . $e;
                    return $this->sendError($message);

                }

                if ($e instanceof \Error) {
                    $message = "There was some internal error" . ', errors:' . $e->getMessage();
                    return $this->sendError($message);
                }

                $statusCode = 500;
                if (method_exists($e, 'getStatusCode')) {
                    $statusCode = $e->getStatusCode();
                }

                return response()->json(
                    [
                        'status' => false,
                        'message' => $e->getMessage(),
                        'code' => $e->getCode()
                    ],
                    $statusCode
                );
            }
            if ($e instanceof Responsable) {
                return $e->toResponse($request);
            }

            $e = $this->prepareException($this->mapException($e));

            if ($response = $this->renderViaCallbacks($request, $e)) {
                return $response;
            }

            return match (true) {
                $e instanceof HttpResponseException => $e->getResponse(),
                $e instanceof AuthenticationException => $this->unauthenticated($request, $e),
                $e instanceof ValidationException => $this->convertValidationExceptionToResponse($e, $request),
                default => $this->renderExceptionResponse($request, $e),
            };
        });
    })->create();
