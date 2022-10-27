<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Lot;
use App\Module\Lot\CreateNewLot\Command as CreateLotCommand;
use App\Module\Lot\CreateNewLot\Handler as CreateLotHandler;
use App\Module\Lot\SearchList\Command as SearchListCommand;
use App\Module\Lot\SearchList\Handler as SearchListHandler;
use App\Module\Lot\UpdateLot\Command as UpdateLotCommand;
use App\Module\Lot\UpdateLot\Handler as UpdateLotHandler;
use App\Repository\LotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class LotController extends AbstractController
{
    private Serializer             $serializer;
    private EntityManagerInterface $em;

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $this->serializer = $serializer;
        $this->em = $em;
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/lots', name: 'lots-list', methods: 'GET')]
    public function getLotsList(Request $request, SearchListHandler $handler): Response
    {
        $command = new SearchListCommand(
            (int)$request->query->get('page', 1),
            (int)$request->query->get('limit', 20),
            (string)$request->query->get('order') ?: null,
            (string)$request->query->get('dest') ?: null,
        );
        $lots = $handler->handle($command);
        $data = $this->serializer->normalize($lots);
        return $this->json($data);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/lots', name: 'lots-create', methods: 'POST')]
    public function createLot(CreateLotHandler $handler, Request $request): Response
    {
        /** @var CreateLotCommand $command */
        $command = $this->serializer->deserialize($request->getContent(), CreateLotCommand::class, JsonEncoder::FORMAT);
        $lot = $handler->handle($command);
        $this->em->persist($lot);
        $this->em->flush();

        $data = $this->serializer->normalize($lot);
        return $this->json($data);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/lots/{lot<\d+>}', name: 'lots-get-one', methods: 'GET')]
    public function getLot(Lot $lot): Response
    {
        $data = $this->serializer->normalize($lot);
        return $this->json($data);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/lots/{lot<\d+>}', name: 'lots-update-one', methods: 'PATCH')]
    public function patchLot(Lot $lot, Request $request, UpdateLotHandler $handler): Response
    {
        /** @var UpdateLotCommand $command */
        $command = $this->serializer->deserialize($request->getContent(), UpdateLotCommand::class, JsonEncoder::FORMAT);
        $lot = $handler->handle($lot, $command);
        $this->em->flush();
        $data = $this->serializer->normalize($lot);
        return $this->json($data);
    }
}
