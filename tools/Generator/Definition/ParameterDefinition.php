<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tools\Generator\Definition;

use GoSuccess\Bunny\Tools\Generator\PhpType;

/**
 * One parameter of a resource method.
 */
final readonly class ParameterDefinition
{
    public const string PATH = 'path';
    public const string QUERY = 'query';
    /** A property of a flattened request body. */
    public const string BODY = 'body';
    /** The request body model itself. */
    public const string PAYLOAD = 'payload';
    /** A path parameter the client supplies, e.g. the Stream library ID. */
    public const string CLIENT = 'client';

    /**
     * @param string      $specName Name in the specification (query key, path placeholder or body key).
     * @param string|null $default  PHP code of the default value; null for required parameters.
     */
    public function __construct(
        public string $specName,
        public string $phpName,
        public PhpType $type,
        public string $location,
        public bool $nullable,
        public ?string $default,
        public ?string $description,
    ) {}

    public function isRequired(): bool
    {
        return $this->default === null;
    }
}
