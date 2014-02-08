<?php
/**
 * @author Ivan Matveev <Redjiks@gmail.com>.
 */

namespace Redjik\WikiBundle\EventListener;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Class ExceptionListener
 * Custom exception handler
 *
 * @package Redjik\WikiBundle\EventListener
 */
class ExceptionListener
{
    /**
     * Generates error Response
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getException();
        //@TODO render template here ffs
        $message = '<h1>Страница не найдена</h1>
        <p>Вы попали на эту страницу по ошибке.</p>
        <p><a href="/">Вернуться в начало</a></p>
        <p><a href="/add">Добавить страницу</a></p>
        ';

        // Customize your response object to display the exception details
        $response = new Response();
        $response->setContent($message);

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
            $event->setResponse($response);
        }

    }
}