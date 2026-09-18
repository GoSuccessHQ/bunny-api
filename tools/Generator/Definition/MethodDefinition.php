<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tools\Generator\Definition;

use GoSuccess\Bunny\Tools\Generator\Config\MethodConfig;
use GoSuccess\Bunny\Tools\Generator\Config\PaginationConfig;
use GoSuccess\Bunny\Tools\Generator\Operation;
use GoSuccess\Bunny\Tools\Generator\PhpType;

/**
 * A resource method to generate.
 */
final class MethodDefinition
{
    /**
     * Parameters in signature order.
     *
     * @var list<ParameterDefinition>
     */
    public array $parameters = [];

    /** The return type; null means void. For paginated methods: the item type. */
    public ?PhpType $returns = null;

    /** Whether a successful response may come without a body. */
    public bool $nullable = false;

    /** Envelope property holding the payload. */
    public ?string $unwrap = null;

    public ?PaginationConfig $pagination = null;

    public function __construct(
        public readonly MethodConfig $config,
        public readonly Operation $operation,
    ) {}

    /**
     * @return list<ParameterDefinition>
     */
    public function parametersIn(string $location): array
    {
        return array_values(array_filter($this->parameters, static fn(ParameterDefinition $parameter): bool => $parameter->location === $location));
    }

    public function payload(): ?ParameterDefinition
    {
        return $this->parametersIn(ParameterDefinition::PAYLOAD)[0] ?? null;
    }
}
