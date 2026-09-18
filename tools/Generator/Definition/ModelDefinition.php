<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tools\Generator\Definition;

/**
 * A data model to generate.
 */
final class ModelDefinition
{
    /** @var list<PropertyDefinition> */
    public array $properties = [];

    /** Whether the model is sent in a request payload. */
    public bool $request = false;

    /** Whether the model is read from a response. */
    public bool $response = false;

    /**
     * @param string $class  Fully qualified class name.
     * @param string $source Schema name, or "Schema.property" for inline objects.
     */
    public function __construct(
        public readonly string $class,
        public readonly string $source,
        public readonly ?string $description,
        public readonly bool $deprecated,
    ) {}
}
