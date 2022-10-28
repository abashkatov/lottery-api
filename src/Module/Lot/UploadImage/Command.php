<?php

declare(strict_types=1);

namespace App\Module\Lot\UploadImage;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    private const ALLOWED_FORMATS = ['jpeg', 'jpg', 'png'];

    private const FILE_SIZE_MIN_BYTES = 10;
    private const FILE_SIZE_MAX_BYTES = 200 * 1024;

    private UploadedFile $file;

    #[Assert\Choice(choices: self::ALLOWED_FORMATS)]
    private string $format;

    #[Assert\GreaterThanOrEqual(self::FILE_SIZE_MIN_BYTES)]
    #[Assert\LessThanOrEqual(self::FILE_SIZE_MAX_BYTES)]
    private int $size;

    public function __construct(UploadedFile $file)
    {
        $this->file   = $file;
        $this->format = $file->getClientOriginalExtension();
        $this->size   = $file->getSize() ?: 0;
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getSize(): int
    {
        return $this->size;
    }
}
