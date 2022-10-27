<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Entity\Image;
use App\Service\UploadImageService;
use App\ValueObject\ImageData;

class ImageDataMapper
{
    private UploadImageService $uploadImageService;

    public function __construct(UploadImageService $uploadImageService)
    {
        $this->uploadImageService = $uploadImageService;
    }

    public function buildImageData(Image $image): ImageData
    {
        return new ImageData(
            $image->getId(),
            $image->getLot()?->getId() ?? 0,
            $this->uploadImageService->getImageFullUrl($image)
        );
    }
}
