<?php

declare(strict_types=1);

namespace App\ValueObject;

class ImageData
{
    public readonly int $id;
    public readonly int $lotId;
    public readonly string $url;

    public function __construct(int $id, int $lotId, string $url)
    {
        $this->id    = $id;
        $this->lotId = $lotId;
        $this->url   = $url;
    }
}
