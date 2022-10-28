<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Lot;
use App\Module\Bid\MakeBid\Command as MakeBidCommand;
use App\Module\Bid\MakeBid\Handler as MakeBidHandler;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class BidController extends AbstractController
{
    private Serializer $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @throws \Throwable
     * @throws Exception
     */
    #[Route('/lots/{lot<\d+>}/bids', name: 'bids-bid', methods: 'POST')]
    public function bid(Request $request, Lot $lot, MakeBidHandler $handler): Response
    {
        $userVkId = (int)$request->headers->get('X-VK-ID');
        if ($lot->getAuthorId() === $userVkId) {
            throw new AccessDeniedHttpException('It is forbidden to bid on your own lots');
        }
        /** @var MakeBidCommand $command */
        $command = $this->serializer->deserialize($request->getContent(), MakeBidCommand::class, JsonEncoder::FORMAT);
        $command->setUserId($userVkId);
        $isBetMade = $handler->handle($lot, $command);

        return $isBetMade
            ? $this->json(null, Response::HTTP_CREATED)
            : $this->json(null, Response::HTTP_NOT_MODIFIED);
    }

}
