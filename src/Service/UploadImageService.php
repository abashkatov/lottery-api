<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Image;
use App\Entity\Lot;
use App\Module\Lot\UploadImage\Command;
use App\ValueObject\ImageData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\UrlHelper;

class UploadImageService
{
    private string                 $imagesDirectory;
    private UrlHelper              $urlHelper;
    private EntityManagerInterface $em;

    public function __construct(string $imagesDirectory, UrlHelper $urlHelper, EntityManagerInterface $em)
    {
        $this->imagesDirectory = $imagesDirectory;
        $this->urlHelper = $urlHelper;
        $this->em = $em;
    }

    public function buildImageData(Image $image): ImageData
    {
        return new ImageData(
            $image->getId(),
            $image->getLot()?->getId() ?? 0,
            $this->getImageFullUrl($image)
        );
    }

    public function uploadImage(Lot $lot, Command $command): Image
    {
        $image = new Image();
        $image->setLot($lot);
        $image->setExt($command->getFormat());
        $lot->addImage($image);
        $this->em->persist($image);
        $this->em->flush();
        $command->getFile()->move(
            $this->getFullFileDirPath($lot),
            $this->getFileName($image)
        );
        return $image;
    }

    public function getFileName(Image $image): string
    {
        return $image->getId() . '.' . $image->getExt();
    }

    private function getImageFullUrl(Image $image): string
    {
        return $this->urlHelper->getAbsoluteUrl(
            sprintf(
                '/uploads/images/%s',
                $this->getRelativePath($image)
            )
        );
    }

    private function getFullFileDirPath(Lot $lot): string
    {
        return sprintf(
            '%s/%s/',
            $this->imagesDirectory,
            $this->getRelativeDir($lot)
        );
    }

    private function getRelativeDir(?Lot $lot): string
    {
        $lotId = $lot?->getId() ?? 0;
        return sprintf(
            '%d/%d/',
            $lotId % 1000,
            $lotId
        );
    }

    private function getRelativePath(Image $image): string
    {
        return sprintf(
            '%s/%s.%s',
            $this->getRelativeDir($image->getLot()),
            $image->getId(),
            $image->getExt()
        );
    }
}
