<?php
namespace ApiBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ApiEventListener
{
    
    /**
     * Kernel exception listener
     *
     * @param GetResponseForExceptionEvent $event exception event
     *
     * @codeCoverageIgnore
     *
     * @return HTTP symfony Response
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $exceptionCode = $exception->getCode();
        $exceptionMessage = json_decode($exception->getMessage(), false);
        if (json_last_error()) {
            $exceptionMessage = $exception->getMessage();
        }

        $response = [
            'link'=>'http://some.url/docs',
            'code'=>$exceptionCode,
            'message'=>$exceptionMessage
        ];

        $userResponse = new Response(json_encode($response));
        if ($exceptionCode > 400000) {
            $userResponse->headers->set('X-Status-Code', 400);
            $code = 400;
        } else {
            $userResponse->headers->set('X-Status-Code', 404);
            $code = 404;
        }
        $userResponse->setStatusCode($code);
        $event->setResponse($userResponse);
    }
}
