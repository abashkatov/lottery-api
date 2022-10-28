<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Lot;
use App\Mapper\ImageDataMapper;
use App\Mapper\LotDataMapper;
use App\Module\Lot\CreateNewLot\Command as CreateLotCommand;
use App\Module\Lot\CreateNewLot\Handler as CreateLotHandler;
use App\Module\Lot\SearchList\Command as SearchListCommand;
use App\Module\Lot\SearchList\Handler as SearchListHandler;
use App\Module\Lot\UpdateLot\Command as UpdateLotCommand;
use App\Module\Lot\UpdateLot\Handler as UpdateLotHandler;
use App\Module\Lot\UploadImage\Command as UploadImageCommand;
use App\Module\Lot\UploadImage\Handler as UploadImageHandler;
use App\Repository\LotRepository;
use App\ValueObject\LotData;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class LotController extends AbstractController
{
    private Serializer             $serializer;
    private EntityManagerInterface $em;
    private LotDataMapper          $lotDataMapper;
    private LoggerInterface        $analyticsLogger;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        LotDataMapper $lotDataMapper,
        LoggerInterface $analyticsLogger,
    )
    {
        $this->serializer = $serializer;
        $this->em = $em;
        $this->lotDataMapper = $lotDataMapper;
        $this->analyticsLogger = $analyticsLogger;
    }

    /**
     * @throws ExceptionInterface
     * @throws Exception
     */
    #[Route('/lots/counters', name: 'lots-counters', methods: 'GET')]
    public function getCounters(Request $request, LotRepository $repository): Response
    {
        $userVkId = (int)$request->headers->get('X-VK-ID');
        $counters = $repository->getCounters($userVkId);
        $data = $this->serializer->normalize($counters);
        return $this->json($data);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/lots', name: 'lots-list', methods: 'GET')]
    public function getLotsList(Request $request, SearchListHandler $handler): Response
    {
        $userVkId = (int)$request->headers->get('X-VK-ID');
        $command = new SearchListCommand(
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 20),
            (string)$request->query->get('order') ?: null,
            (string)$request->query->get('dest') ?: null,
            $request->query->get('isMy') ?: null,
            $request->query->get('status') ?: null,
            $request->query->getBoolean('isOnlyBet') ?? false,
            $userVkId,
        );
        $lots = $handler->handle($command);
        $lotDataMapper = $this->lotDataMapper;
        $lotsData = \array_map(
            static fn(Lot $lot): LotData => $lotDataMapper->buildLotData($lot),
            $lots
        );
        $data = $this->serializer->normalize($lotsData);
        return $this->json($data);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/lots', name: 'lots-create', methods: 'POST')]
    public function createLot(CreateLotHandler $handler, Request $request): Response
    {
        $userVkId = (int)$request->headers->get('X-VK-ID');
        /** @var CreateLotCommand $command */
        $command = $this->serializer->deserialize($request->getContent(), CreateLotCommand::class, JsonEncoder::FORMAT);
        $lot = $handler->handle($command, $userVkId);
        $this->em->persist($lot);
        $this->em->flush();
        $this->analyticsLogger->info('create lot', [
            'user_id' => $userVkId,
            'lot' => [
                'id' => $lot->getId(),
                'price_start' => $lot->getPriceStart(),
                'price_step' => $lot->getPriceStep(),
                'title' => $lot->getTitle(),
            ],
        ]);
        $lotData = $this->lotDataMapper->buildLotData($lot);
        $data = $this->serializer->normalize($lotData);
        return $this->json($data);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/lots/{lot<\d+>}', name: 'lots-get-one', methods: 'GET')]
    public function getLot(Lot $lot): Response
    {
        $lotData = $this->lotDataMapper->buildLotData($lot);
        $data = $this->serializer->normalize($lotData);
        return $this->json($data);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/lots/{lot<\d+>}', name: 'lots-update-one', methods: 'PATCH')]
    public function patchLot(Lot $lot, Request $request, UpdateLotHandler $handler): Response
    {
        $userVkId = (int)$request->headers->get('X-VK-ID');
        if ($lot->getAuthorId() !== $userVkId) {
            throw new AccessDeniedHttpException('Permissions denied');
        }
        /** @var UpdateLotCommand $command */
        $command = $this->serializer->deserialize($request->getContent(), UpdateLotCommand::class, JsonEncoder::FORMAT);
        $command->setPreviousStatus($lot->getStatus());
        $lot = $handler->handle($lot, $command);
        $this->em->flush();
        $lotData = $this->lotDataMapper->buildLotData($lot);
        $data = $this->serializer->normalize($lotData);
        return $this->json($data);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/lots/{lot<\d+>}/image', name: 'lots-image-upload', methods: ['POST'])]
    public function index(Lot $lot, Request $request, UploadImageHandler $handler, ImageDataMapper $imageDataMapper): Response
    {
        $userVkId = (int)$request->headers->get('X-VK-ID');
        if ($lot->getAuthorId() !== $userVkId) {
            throw new AccessDeniedHttpException('Permissions denied');
        }
        $file = $request->files->get('image');
        if (!$file instanceof UploadedFile) {
            throw new \InvalidArgumentException('A required file is missing');
        }
        $command = new UploadImageCommand($file);
        $image = $handler->handle($lot, $command);
        $imageDto = $imageDataMapper->buildImageData($image);
        $data = $this->serializer->normalize($imageDto);
        return $this->json($data);
    }
}
