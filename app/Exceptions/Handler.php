<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    private $statusCode;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
        \League\OAuth2\Server\Exception\OAuthServerException::class,
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
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Throwable $exception)
    {
        if (strpos($request->url(), '/api/') !== false || strpos($request->url(), '/paylesuccess/') !== false) {
            \Log::debug('API Request Exception - '.$request->url().' - '.$exception->getMessage().(! empty($request->all()) ? ' - '.json_encode($request->except(['password'])) : ''));

            if ($exception instanceof AuthorizationException) {
                return $this->setStatusCode(403)->respondWithError($exception->getMessage());
            }

            if ($exception instanceof MethodNotAllowedHttpException) {
                return $this->setStatusCode(405)->respondWithError('Please check HTTP Request Method. - MethodNotAllowedHttpException');
            }

            if ($exception instanceof AuthenticationException) {
                return $this->setStatusCode(401)->respondWithError('Unauthenticated');
            }

            if ($exception instanceof NotFoundHttpException) {
                return $this->setStatusCode(404)->respondWithError('Please check your URL to make sure request is formatted properly. - NotFoundHttpException');
            }

            if ($exception instanceof GeneralException) {
                return $this->setStatusCode(403)->respondWithError($exception->getMessage());
            }

            if ($exception instanceof ModelNotFoundException) {
                return $this->setStatusCode(403)->respondWithError('The requested item is not available');
            }

            if ($exception instanceof ValidationException) {
                \Log::debug('API Validation Exception - '.json_encode($exception->validator->messages()));
                $error = '';
                if ($exception->validator->fails()) {
                    $messages = $exception->validator->messages()->toArray();
                    foreach ($messages as $key => $message) {
                        $error = $message[0];
                        break;
                    }
                }

                return $this->setStatusCode(422)->respondWithError($error);
            }

            /*
            * Redirect if token mismatch error
            * Usually because user stayed on the same screen too long and their session expired
            */
            if ($exception instanceof UnauthorizedHttpException) {
                switch (get_class($exception->getPrevious())) {
                    case \App\Exceptions\Handler::class:
                        return $this->setStatusCode($exception->getStatusCode())->respondWithError('Token has not been provided.');
                }
            } else {
                return $this->setStatusCode(500)->respondWithError($exception->getMessage());
            }

        }

        /*
         * Redirect if token mismatch error
         * Usually because user stayed on the same screen too long and their session expired
         */
        if ($exception instanceof TokenMismatchException) {
            if (strpos($request->url(), '/api/') !== false) {
                return $this->setStatusCode(401)->respondWithError('Unauthenticated');
            }

        }

        /*
         * All instances of GeneralException redirect back with a flash message to show a bootstrap alert-error
         */
        if ($exception instanceof GeneralException) {
            session()->flash('dontHide', $exception->dontHide);

            return redirect()->back()->withInput()->withFlashDanger($exception->getMessage());
        }

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if (strpos($request->url(), '/api/') !== false) {
            return $this->setStatusCode(401)->respondWithError('Unauthenticated');
        }

    }

    /**
     * get the status code.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * set the status code.
     *
     * @param [type] $statusCode [description]
     * @return Handler
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * respond with error.
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithError($message)
    {
        return $this->respond([
            'success' => false,
            'status' => $this->getStatusCode(),
            'message' => $message,
        ]);
    }

    /**
     * Respond.
     *
     * @param  array  $data
     * @param  array  $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }
}
