<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tools\Generator;

use GoSuccess\Bunny\Tools\Generator\Config\ApiConfig;
use GoSuccess\Bunny\Tools\Generator\Definition\EnumDefinition;
use GoSuccess\Bunny\Tools\Generator\Definition\ModelDefinition;
use GoSuccess\Bunny\Tools\Generator\Definition\PropertyDefinition;
use RuntimeException;

/**
 * Maps schemas to PHP types and collects the enums and models to generate.
 */
final class Registry
{
    /** @var array<string, EnumDefinition> Keyed by class. */
    public array $enums = [];

    /** @var array<string, ModelDefinition> Keyed by class. */
    public array $models = [];

    public readonly EnumBuilder $enumBuilder;

    /** @var array<string, string> Class => source, to detect two sources claiming one class. */
    private array $claims = [];

    /** @var array<string, string> Model class => structure of its schema, see signature(). */
    private array $signatures = [];

    public function __construct(
        public readonly Spec $spec,
        public readonly ApiConfig $config,
    ) {
        $this->enumBuilder = new EnumBuilder();
    }

    /**
     * Map a schema to a PHP type, registering the enums and models it needs.
     *
     * @param string $path Location used to name inline objects, e.g. "PullZoneModel.Hostnames".
     */
    public function type(Schema $schema, string $path): PhpType
    {
        if (\in_array($path, $this->config->stringMaps, true)) {
            return PhpType::mapOf(PhpType::scalar(PhpType::STRING));
        }

        $name = $schema->resolvedName();

        if ($name !== null && $this->spec->hasSchema($name)) {
            $component = $this->spec->schema($name);

            if ($component->isEnum()) {
                return $this->enumType($component);
            }

            if ($this->isModel($component)) {
                return new PhpType(PhpType::MODEL, $this->model($component, $name));
            }

            // A component that merely aliases a scalar, list or map.
            return $this->structural($component, $name);
        }

        $resolved = $schema->resolve();

        if ($this->isModel($resolved)) {
            return new PhpType(PhpType::MODEL, $this->model($resolved, $path));
        }

        return $this->structural($resolved, $path);
    }

    /**
     * Register a component schema as a model (e.g. for hand-written code).
     */
    public function requireModel(string $schemaName): string
    {
        $schema = $this->spec->schema($schemaName);

        if (!$this->isModel($schema)) {
            throw new RuntimeException("{$schemaName} is not an object schema.");
        }

        return $this->model($schema, $schemaName);
    }

    /**
     * Mark a type (and everything it contains) as used in requests or responses.
     */
    public function markUsage(PhpType $type, bool $request): void
    {
        $this->mark($type, $request, []);
    }

    /**
     * @param array<string, true> $seen
     */
    private function mark(PhpType $type, bool $request, array $seen): void
    {
        if ($type->isCollection()) {
            $this->mark($type->itemOrFail(), $request, $seen);

            return;
        }

        if ($type->kind !== PhpType::MODEL || isset($seen[$type->classOrFail()])) {
            return;
        }

        $model = $this->models[$type->classOrFail()];
        $seen[$model->class] = true;

        if ($request) {
            $model->request = true;
        } else {
            $model->response = true;
        }

        foreach ($model->properties as $property) {
            if (!$request || !$property->readOnly) {
                $this->mark($property->type, $request, $seen);
            }
        }
    }

    public function isModel(Schema $schema): bool
    {
        return $schema->properties() !== [] || ($schema->variants('allOf') !== [] && $schema->additionalProperties() === null);
    }

    private function enumType(Schema $component): PhpType
    {
        $schemaName = $component->name ?? throw new RuntimeException('Enum schemas must be components.');
        $class = $this->className($schemaName, 'Enum');
        $definition = $this->enumBuilder->build($component, $class, $this->config->enumCases[$schemaName] ?? null);
        $existing = $this->enums[$class] ?? null;

        if ($existing !== null) {
            if ($existing->signature() !== $definition->signature()) {
                throw new RuntimeException("{$schemaName} and " . implode(', ', $existing->schemas) . " both map to {$class} but differ.");
            }

            if (!\in_array($schemaName, $existing->schemas, true)) {
                $this->enums[$class] = new EnumDefinition($class, $existing->backing, $existing->cases, $existing->description, [...$existing->schemas, $schemaName]);
            }
        } else {
            $this->enums[$class] = $definition;
        }

        return new PhpType(PhpType::ENUM, $class, $definition->backing);
    }

    private function model(Schema $schema, string $source): string
    {
        $class = $this->className($source, 'Model');

        if (isset($this->models[$class])) {
            // Several sources may share a class if their structure is identical,
            // e.g. copies of one inline object.
            if ($this->models[$class]->source !== $source && $this->signatures[$class] !== self::signature($schema)) {
                throw new RuntimeException("{$source} and {$this->models[$class]->source} both map to {$class} but differ.");
            }

            return $class;
        }

        $this->signatures[$class] = self::signature($schema);

        $model = new ModelDefinition($class, $source, $schema->description(), $schema->isDeprecated());
        // Register before the properties, so self-references terminate.
        $this->models[$class] = $model;

        $required = $schema->required();
        $names = [];

        foreach ($schema->properties() as $json => $property) {
            // Errors in successful responses become exceptions in the connection.
            if (\in_array($json, $this->config->errorProperties, true)) {
                continue;
            }

            $phpName = $this->config->properties["{$source}.{$json}"] ?? Naming::camel($json);

            if (isset($names[strtolower($phpName)])) {
                throw new RuntimeException("{$source}: properties {$json} and {$names[strtolower($phpName)]} both map to \${$phpName}.");
            }

            $names[strtolower($phpName)] = $json;
            $resolved = $property->resolve();
            $type = $this->type($property, "{$source}.{$json}");
            $description = $property->description() ?? $resolved->description();

            if ($type->kind === PhpType::ENUM && $description !== null) {
                // "... ForceSSL = 0, Redirect = 1, ..." duplicates (and often lags behind) the enum.
                $description = trim((string) preg_replace('/\s*[A-Za-z_][A-Za-z0-9_]*\s*=\s*-?\d+(?:\s*,\s*[A-Za-z_][A-Za-z0-9_]*\s*=\s*-?\d+)+\s*\.?$/', '', $description));
                $description = $description === '' ? null : $description;
            }

            $model->properties[] = new PropertyDefinition(
                jsonName: $json,
                phpName: $phpName,
                type: $type,
                nullable: $property->isNullable() || $resolved->isNullable() || \in_array("{$source}.{$json}", $this->config->nullableProperties, true),
                required: \in_array($json, $required, true),
                readOnly: $property->isReadOnly() || $resolved->isReadOnly(),
                deprecated: $property->isDeprecated(),
                description: $description,
            );
        }

        return $class;
    }

    private function structural(Schema $schema, string $path): PhpType
    {
        return match ($schema->type()) {
            'string' => \in_array($schema->format(), ['date-time', 'datetime'], true) ? PhpType::scalar(PhpType::DATE) : PhpType::scalar(PhpType::STRING),
            'integer' => PhpType::scalar(PhpType::INT),
            'number' => PhpType::scalar(PhpType::FLOAT),
            'boolean' => PhpType::scalar(PhpType::BOOL),
            'array' => PhpType::listOf($schema->items() === null ? PhpType::scalar(PhpType::MIXED) : $this->type($schema->items(), "{$path}[]")),
            'object', null => $this->objectType($schema, $path),
            default => throw new RuntimeException("{$path}: unsupported type {$schema->type()}."),
        };
    }

    private function objectType(Schema $schema, string $path): PhpType
    {
        $values = $schema->additionalProperties();

        if ($values !== null && $values->node !== []) {
            return PhpType::mapOf($this->type($values, "{$path}{}"));
        }

        return $schema->type() === 'object' || $values !== null
            ? PhpType::scalar(PhpType::OBJECT)
            : PhpType::scalar(PhpType::MIXED);
    }

    /**
     * @param 'Enum'|'Model' $kind
     */
    private function className(string $source, string $kind): string
    {
        $configured = $this->config->schemas[$source] ?? null;

        if ($configured === false) {
            throw new RuntimeException("{$source} is excluded in the configuration but still referenced.");
        }

        $short = $configured ?? $this->automaticName($source);
        $class = $this->config->fqcn($kind, $short);
        $claimedBy = $this->claims[$class] ?? null;

        // Only a configured name may be shared, and only by identical structures
        // (checked in model()); automatic names must not collide.
        if ($claimedBy !== null && $claimedBy !== $source && $kind === 'Model' && ($configured === null || !isset($this->config->schemas[$claimedBy]))) {
            throw new RuntimeException("{$source} and {$claimedBy} both map to {$class}.");
        }

        $this->claims[$class] ??= $source;

        return $class;
    }

    /**
     * The structure of an object schema without its descriptions.
     */
    private static function signature(Schema $schema): string
    {
        $strip = static function (mixed $node) use (&$strip): mixed {
            if (!\is_array($node)) {
                return $node;
            }

            unset($node['description'], $node['example'], $node['title']);

            return array_map($strip, $node);
        };

        return (string) json_encode($strip($schema->node));
    }

    /**
     * "PullZoneModel" → "PullZone"; inline objects "Parent.property" → "ParentProperty",
     * list items "Parent.property[]" → "ParentPropertyItem".
     */
    private function automaticName(string $source): string
    {
        if (!str_contains($source, '.')) {
            return Naming::pascal((string) preg_replace('/(?<=.)Model$/', '', $source));
        }

        [$parent, $property] = explode('.', $source, 2);
        $parentClass = $this->config->schemas[$parent] ?? null;
        $parentShort = \is_string($parentClass) ? $parentClass : $this->automaticName($parent);
        $suffix = str_replace(['[]', '{}'], ['Item', 'Value'], $property);

        return $parentShort . Naming::pascal($suffix);
    }
}
