<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exception\InvalidParamsException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();
        if ($exception instanceof UniqueConstraintViolationException) {
            $response = new JsonResponse([
                'error' => ['messages' => ['Запись с такими данными уже существует']],
            ], Response::HTTP_BAD_REQUEST);
            $event->setResponse($response);
            return;
        }
        if ($exception instanceof InvalidParamsException) {
            $messages = [];
            if ($exception->getErrors() !== null) {
                foreach ($exception->getErrors() as $error) {
                    $messages[] = sprintf(
                        '%s: %s',
                        $error->getPropertyPath(),
                        $error->getMessage()
                    );
                }
            } else {
                $messages[] = $exception->getMessage();
            }
            $response = new JsonResponse([
                'error' => ['messages' => $messages],
            ], Response::HTTP_BAD_REQUEST);
            $event->setResponse($response);
            return;
        }
        if ($exception instanceof NotFoundHttpException) {
            $response = new JsonResponse([
                'error' => ['messages' => ['Object not found']],
            ], Response::HTTP_NOT_FOUND);
            $event->setResponse($response);
            return;
        }
        if ($exception instanceof AccessDeniedHttpException) {
            $response = new JsonResponse([
                'error' => ['messages' => ['Доступ запрещён']],
            ], Response::HTTP_FORBIDDEN);
            $event->setResponse($response);
        }
        if ($exception instanceof \DomainException) {
            $response = new JsonResponse([
                'error' => ['messages' => [$exception->getMessage()]],
            ], Response::HTTP_BAD_REQUEST);
            $event->setResponse($response);
        }
    }
}
