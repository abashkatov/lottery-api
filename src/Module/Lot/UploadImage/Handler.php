<?php

declare(strict_types=1);

namespace App\Module\Lot\UploadImage;

use App\Entity\Image;
use App\Entity\Lot;
use App\Exception\InvalidParamsException;
use App\Service\UploadImageService;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Handler
{
    private ValidatorInterface $validator;
    private UploadImageService $uploadImageService;

    public function __construct(ValidatorInterface $validator, UploadImageService $uploadImageService)
    {
        $this->validator = $validator;
        $this->uploadImageService = $uploadImageService;
    }

    public function handle(Lot $lot, Command $file): Image
    {
        $errors = $this->validator->validate($file);
        if (count($errors) > 0) {
            throw new InvalidParamsException("Invalid uploaded file params", $errors);
        }

        return $this->uploadImageService->uploadImage($lot, $file);
    }
}



