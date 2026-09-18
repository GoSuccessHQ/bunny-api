<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tools\Generator;

use RuntimeException;

/**
 * A loaded OpenAPI 3.0 document.
 */
final class Spec
{
    private const array METHODS = ['get', 'post', 'put', 'patch', 'delete'];

    /** @var array<string, Schema> */
    private array $schemas = [];

    /** @var array<string, Operation>|null */
    private ?array $operations = null;

    /**
     * @param array<array-key, mixed> $document
     */
    private function __construct(
        public readonly string $name,
        public readonly string $file,
        private readonly array $document,
    ) {}

    public static function load(string $name, string $file): self
    {
        $json = file_get_contents($file);

        if ($json === false) {
            throw new RuntimeException("Unable to read {$file}.");
        }

        $document = json_decode($json, true, 512, \JSON_THROW_ON_ERROR);

        if (!\is_array($document)) {
            throw new RuntimeException("{$file} does not contain a JSON object.");
        }

        return new self($name, $file, $document);
    }

    public function hasSchema(string $name): bool
    {
        return isset($this->components()[$name]);
    }

    public function schema(string $name): Schema
    {
        if (isset($this->schemas[$name])) {
            return $this->schemas[$name];
        }

        $node = $this->components()[$name] ?? null;

        if (!\is_array($node)) {
            throw new RuntimeException("Unknown schema {$name} in {$this->name}.");
        }

        return $this->schemas[$name] = new Schema($this, $node, $name);
    }

    /**
     * @return list<string>
     */
    public function schemaNames(): array
    {
        return array_map(strval(...), array_keys($this->components()));
    }

    /**
     * Operations keyed by an identifier: the operationId, or "METHOD /path"
     * for operations without one.
     *
     * @return array<string, Operation>
     */
    public function operations(): array
    {
        if ($this->operations !== null) {
            return $this->operations;
        }

        $operations = [];
        $paths = \is_array($this->document['paths'] ?? null) ? $this->document['paths'] : [];
        $idCounts = [];

        foreach ($paths as $item) {
            if (!\is_array($item)) {
                continue;
            }

            foreach (self::METHODS as $method) {
                $operationId = \is_array($item[$method] ?? null) ? Operation::operationId($item[$method]) : null;

                if ($operationId !== null) {
                    $idCounts[$operationId] = ($idCounts[$operationId] ?? 0) + 1;
                }
            }
        }

        foreach ($paths as $path => $item) {
            if (!\is_array($item)) {
                continue;
            }

            $shared = \is_array($item['parameters'] ?? null) ? $item['parameters'] : [];

            foreach (self::METHODS as $method) {
                $node = $item[$method] ?? null;

                if (!\is_array($node)) {
                    continue;
                }

                // An operationId used twice (e.g. for PUT and PATCH) identifies neither.
                $ambiguous = ($idCounts[Operation::operationId($node) ?? ''] ?? 0) > 1;
                $operation = new Operation($this, strtoupper($method), (string) $path, $node, $shared, $ambiguous);
                $operations[$operation->id] = $operation;
            }
        }

        return $this->operations = $operations;
    }

    public function operation(string $id): Operation
    {
        return $this->operations()[$id] ?? throw new RuntimeException("Unknown operation {$id} in {$this->name}.");
    }

    /**
     * Resolve a local reference such as `#/components/schemas/PullZoneModel`.
     */
    public function refTarget(string $ref): string
    {
        if (!str_starts_with($ref, '#/components/schemas/')) {
            throw new RuntimeException("Unsupported reference {$ref} in {$this->name}.");
        }

        return substr($ref, \strlen('#/components/schemas/'));
    }

    /**
     * @return array<array-key, mixed>
     */
    private function components(): array
    {
        $components = $this->document['components'] ?? null;
        $schemas = \is_array($components) ? ($components['schemas'] ?? null) : null;

        return \is_array($schemas) ? $schemas : [];
    }
}
