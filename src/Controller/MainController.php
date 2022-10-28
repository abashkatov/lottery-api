<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    private LoggerInterface $logger;
    private LoggerInterface $analyticsLogger;

    public function __construct(LoggerInterface $logger, LoggerInterface $analyticsLogger)
    {
        $this->logger = $logger;
        $this->analyticsLogger = $analyticsLogger;
    }

    #[Route('/', name: 'check-health', methods: 'GET')]
    public function checkHealth(): Response
    {
        $this->logger->info('info message', ['something' => 'everything']);
        $this->logger->warning('warning message', ['something1' => 'everything1']);
        $this->analyticsLogger->info('info analytics', ['event' => 'event description']);
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
