<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tests\Support;

use GoSuccess\Bunny\Model\Cast;
use GoSuccess\Bunny\Model\ResponseModel;

final readonly class ExampleModel implements ResponseModel
{
    public function __construct(public ?string $name = null) {}

    public static function fromArray(array $data): static
    {
        return new self(Cast::string($data['Name'] ?? null));
    }
}
