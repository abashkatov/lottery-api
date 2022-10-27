<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Entity\Image;
use App\Entity\Lot;
use App\ValueObject\ImageData;
use App\ValueObject\LotData;

class LotDataMapper
{
    private ImageDataMapper $imageDataMapper;

    public function __construct(ImageDataMapper $imageDataMapper)
    {
        $this->imageDataMapper = $imageDataMapper;
    }

    public function buildLotData(Lot $lot): LotData
    {
        $imageDataMapper = $this->imageDataMapper;
        $imagesData = $lot
            ->getImages()
            ->map(static fn(Image $image): ImageData => $imageDataMapper->buildImageData($image))
            ->toArray();
        return new LotData($lot, $imagesData);
    }
}
