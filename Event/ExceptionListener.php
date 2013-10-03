<?php

namespace Da\ApiClientBundle\Event;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Da\ApiClientBundle\Exception\ApiHttpResponseException;

/**
 * Listener to pass the http exceptions.
 */
class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception =  $event->getException();

        if ($exception instanceof HttpExceptionInterface) {
            $response = new Response();

            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());

            if ($exception instanceof ApiHttpResponseException) {
                $response->setContent($exception->getJsonMessage());
            } else {
                $content = $exception->getMessage();
                try {
                    $content = json_decode($content);
                }
                catch (\Exception $e) {
                }
                $content = json_encode(
                    array(
                        'message' => 'The server returned an error.',
                        'http_code' => $exception->getStatusCode(),
                        'error' => $content
                    ),
                    JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT
                );

                $response->setContent($content);
            }

            $response->headers->set('Content-Type', 'application/json');
            $event->setResponse($response);
        }
    }
}