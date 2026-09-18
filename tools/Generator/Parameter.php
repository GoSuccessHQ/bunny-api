<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tools\Generator;

/**
 * A path, query or header parameter of an operation.
 */
final class Parameter
{
    public readonly string $name;

    public readonly string $in;

    public readonly bool $required;

    public readonly Schema $schema;

    /**
     * @param array<array-key, mixed> $node
     */
    public function __construct(Spec $spec, public readonly array $node)
    {
        $this->name = \is_string($node['name'] ?? null) ? $node['name'] : '';
        $this->in = \is_string($node['in'] ?? null) ? $node['in'] : 'query';
        $this->required = ($node['required'] ?? false) === true || $this->in === 'path';
        $this->schema = new Schema($spec, \is_array($node['schema'] ?? null) ? $node['schema'] : []);
    }

    public function description(): ?string
    {
        $description = $this->node['description'] ?? null;

        return \is_string($description) && trim($description) !== '' ? $description : $this->schema->description();
    }
}
