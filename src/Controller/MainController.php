<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'check-health', methods: 'GET')]
    public function checkHealth(): Response
    {
        return $this->json(['greetings' => 'hello']);
    }
}
