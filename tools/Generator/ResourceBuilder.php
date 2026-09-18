<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tools\Generator;

use GoSuccess\Bunny\Tools\Generator\Config\MethodConfig;
use GoSuccess\Bunny\Tools\Generator\Config\ResourceConfig;
use GoSuccess\Bunny\Tools\Generator\Definition\MethodDefinition;
use GoSuccess\Bunny\Tools\Generator\Definition\ParameterDefinition;
use GoSuccess\Bunny\Tools\Generator\Definition\ResourceDefinition;
use RuntimeException;

/**
 * Turns the resource configuration plus the specification into method definitions.
 */
final class ResourceBuilder
{
    /**
     * Content types that mark an operation as non-JSON; such operations must be
     * implemented by hand.
     */
    private const array RAW_TYPES = ['application/octet-stream', 'text/csv', 'application/pdf'];

    /** @var list<string> */
    public array $notes = [];

    public function __construct(private readonly Registry $registry) {}

    public function build(ResourceConfig $config): ResourceDefinition
    {
        $class = $this->registry->config->fqcn('Resource', $config->class);
        $resource = new ResourceDefinition($config, $class);

        foreach ($config->methods as $name => $methodConfig) {
            $method = $this->method($methodConfig);

            if ($methodConfig->handwritten) {
                if (!$config->handwritten) {
                    throw new RuntimeException("{$config->class}::{$name} is hand-written, but the resource does not mix in its trait.");
                }

                $resource->handwritten[$name] = $method;
            } else {
                $resource->methods[] = $method;
            }
        }

        return $resource;
    }

    private function method(MethodConfig $config): MethodDefinition
    {
        $operation = $this->registry->spec->operation($config->operation);
        $method = new MethodDefinition($config, $operation);
        $context = "{$operation->id} ({$config->name})";

        if ($config->pagination !== null) {
            $method->pagination = $this->registry->config->pagination[$config->pagination]
                ?? throw new RuntimeException("{$context}: unknown pagination style {$config->pagination}.");
        }

        $required = [];
        $optional = [];

        foreach ($this->pathParameters($operation, $config, $context) as $parameter) {
            $required[] = $parameter;
        }

        // Hand-written methods build their own request bodies.
        [$bodyRequired, $bodyOptional] = $config->handwritten ? [[], []] : $this->body($operation, $config, $context);
        $required = [...$required, ...$bodyRequired];

        $queryNames = [];

        foreach ($operation->parametersIn('query') as $parameter) {
            $queryNames[] = $parameter->name;

            if (\in_array($parameter->name, $config->hidden, true)) {
                continue;
            }

            $definition = $this->queryParameter($parameter, $method, $context);

            if ($definition->isRequired()) {
                $required[] = $definition;
            } else {
                $optional[] = $definition;
            }
        }

        $bodyNames = array_map(static fn(ParameterDefinition $parameter): string => $parameter->specName, [...$bodyRequired, ...$bodyOptional]);

        foreach (array_diff($config->required, $queryNames, $bodyNames) as $unknown) {
            throw new RuntimeException("{$context}: {$unknown} is marked required, but is neither a query parameter nor a flattened body property.");
        }

        foreach ($operation->parametersIn('header') as $parameter) {
            if (!\in_array($parameter->name, $config->hidden, true) && strtolower($parameter->name) !== 'accesskey') {
                throw new RuntimeException("{$context}: header parameter {$parameter->name} is not supported; hide it or write the method by hand.");
            }
        }

        $method->parameters = [...$required, ...$optional, ...$bodyOptional];

        // Hand-written methods read their responses themselves; the models they
        // use are listed as extraModels.
        if (!$config->handwritten) {
            $this->response($method, $config, $operation, $context);
        }

        return $method;
    }

    /**
     * @return list<ParameterDefinition>
     */
    private function pathParameters(Operation $operation, MethodConfig $config, string $context): array
    {
        $byName = [];

        foreach ($operation->parametersIn('path') as $parameter) {
            $byName[$parameter->name] = $parameter;
        }

        $definitions = [];

        foreach ($operation->pathPlaceholders() as $placeholder) {
            $parameter = $byName[$placeholder] ?? throw new RuntimeException("{$context}: path placeholder {$placeholder} is not declared.");
            unset($byName[$placeholder]);

            $type = match ($config->parameterTypes[$placeholder] ?? null) {
                null => $this->registry->type($parameter->schema, "{$operation->id}.{$placeholder}"),
                'int' => PhpType::scalar(PhpType::INT),
                'string' => PhpType::scalar(PhpType::STRING),
                default => throw new RuntimeException("{$context}: parameter type of {$placeholder} must be int or string."),
            };

            if (!\in_array($type->kind, [PhpType::INT, PhpType::STRING, PhpType::ENUM, PhpType::DATE], true)) {
                throw new RuntimeException("{$context}: unsupported path parameter type {$type->kind} for {$placeholder}.");
            }

            $isClientParameter = isset($this->registry->config->clientParameters[$placeholder]);

            $definitions[] = new ParameterDefinition(
                specName: $placeholder,
                phpName: $isClientParameter ? $placeholder : ($config->parameters[$placeholder] ?? Naming::camel($placeholder)),
                type: $type,
                location: $isClientParameter ? ParameterDefinition::CLIENT : ParameterDefinition::PATH,
                nullable: false,
                default: null,
                description: $parameter->description(),
            );
        }

        foreach (array_keys($byName) as $stray) {
            // e.g. Edge Scripting declares {uuid} on a path that has no such placeholder.
            $this->notes[] = "{$context}: ignored path parameter {$stray}, which does not occur in {$operation->path}.";
        }

        return $definitions;
    }

    /**
     * @return array{list<ParameterDefinition>, list<ParameterDefinition>}
     */
    private function body(Operation $operation, MethodConfig $config, string $context): array
    {
        if ($config->body === 'none') {
            return [[], []];
        }

        $schema = $config->body !== null ? $this->componentRef($config->body) : $operation->requestSchema();

        if ($schema === null) {
            foreach ($operation->requestContentTypes() as $type) {
                if (!str_contains($type, 'json')) {
                    throw new RuntimeException("{$context}: request body {$type} needs a hand-written method.");
                }
            }

            return [[], []];
        }

        if ($config->flatten) {
            return $this->flattenedBody($schema, $operation, $config, $context);
        }

        $type = $this->registry->type($schema, "{$operation->id}.body");
        $this->registry->markUsage($type, true);

        if ($type->kind !== PhpType::MODEL && $type->kind !== PhpType::MAP) {
            throw new RuntimeException("{$context}: request body of type {$type->kind} is not supported.");
        }

        $required = $operation->isRequestBodyRequired() || $this->registry->config->requestBodiesRequired;
        $name = $config->parameters['@body'] ?? lcfirst(substr($type->class ?? 'Payload', (int) strrpos($type->class ?? '\\Payload', '\\') + 1));
        $definition = new ParameterDefinition(
            specName: '@body',
            phpName: $name === 'array' ? 'payload' : $name,
            type: $type,
            location: ParameterDefinition::PAYLOAD,
            nullable: !$required,
            default: $required ? null : 'null',
            description: $schema->resolve()->description(),
        );

        return $required ? [[$definition], []] : [[], [$definition]];
    }

    /**
     * @return array{list<ParameterDefinition>, list<ParameterDefinition>}
     */
    private function flattenedBody(Schema $schema, Operation $operation, MethodConfig $config, string $context): array
    {
        $resolved = $schema->resolve();
        $source = $schema->resolvedName() ?? "{$operation->id}.body";
        $requiredNames = $resolved->required();
        $required = [];
        $optional = [];

        foreach ($resolved->properties() as $json => $property) {
            if ($property->isReadOnly()) {
                continue;
            }

            $type = $this->registry->type($property, "{$source}.{$json}");
            $this->registry->markUsage($type, true);

            // The body repeats a path parameter, e.g. {"shieldZoneId"} next to
            // /shield-zone/{shieldZoneId}: send the same value.
            if (\in_array($json, $operation->pathPlaceholders(), true)) {
                if (isset($this->registry->config->clientParameters[$json])) {
                    throw new RuntimeException("{$context}: body property {$json} repeats a client parameter, which is not supported.");
                }

                $required[] = new ParameterDefinition(
                    specName: $json,
                    phpName: $config->parameters[$json] ?? Naming::camel($json),
                    type: $type,
                    location: ParameterDefinition::BOUND,
                    nullable: false,
                    default: null,
                    description: null,
                );

                continue;
            }

            // Marked required in the configuration: a value is expected, not null.
            $forced = \in_array($json, $config->required, true);
            $isRequired = $forced || \in_array($json, $requiredNames, true);
            $propertyNullable = !$forced && ($property->isNullable() || $property->resolve()->isNullable());

            $definition = new ParameterDefinition(
                specName: $json,
                phpName: $config->parameters[$json] ?? Naming::camel($json),
                type: $type,
                location: ParameterDefinition::BODY,
                nullable: !$isRequired || $propertyNullable,
                default: $isRequired ? null : 'null',
                description: $property->description() ?? $property->resolve()->description(),
            );

            if ($isRequired) {
                $required[] = $definition;
            } else {
                $optional[] = $definition;
            }
        }

        if ($required === [] && $optional === []) {
            throw new RuntimeException("{$context}: flattened body has no properties.");
        }

        return [$required, $optional];
    }

    private function queryParameter(Parameter $parameter, MethodDefinition $method, string $context): ParameterDefinition
    {
        $type = $this->registry->type($parameter->schema, "{$method->operation->id}.{$parameter->name}");

        if ($type->kind === PhpType::MODEL || $type->kind === PhpType::MAP) {
            throw new RuntimeException("{$context}: query parameter {$parameter->name} of type {$type->kind} is not supported.");
        }

        $name = $method->config->parameters[$parameter->name] ?? Naming::camel($parameter->name);
        $pagination = $method->pagination;
        $required = $parameter->required || \in_array($parameter->name, $method->config->required, true);
        $default = $required ? null : 'null';
        $nullable = !$required;

        $description = $parameter->description();

        if ($pagination !== null && $parameter->name === $pagination->position) {
            $description = $pagination->positionType === 'int'
                ? (($pagination->first ?? 1) === 0 ? 'The number of items to skip.' : 'The page to return, starting at ' . ($pagination->first ?? 1) . '.')
                : 'The position returned by the previous page; null for the first page.';

            if ($pagination->positionType === 'int') {
                $default = (string) ($pagination->first ?? 1);
                $nullable = false;
            }
        } elseif ($pagination !== null && $parameter->name === $pagination->size) {
            $description = 'The number of items per page.';

            if ($pagination->pageSize !== null) {
                $default = (string) $pagination->pageSize;
                $nullable = false;
            }
        }

        return new ParameterDefinition(
            specName: $parameter->name,
            phpName: $name,
            type: $type,
            location: ParameterDefinition::QUERY,
            nullable: $nullable,
            default: $default,
            description: $description,
        );
    }

    /**
     * The payload property of an envelope such as `{"data": …, "error": …}`:
     * an object with the configured envelope property and error properties only.
     */
    private function envelopeProperty(Schema $schema): ?string
    {
        $envelope = $this->registry->config->envelope;
        $properties = array_keys($schema->resolve()->properties());

        if ($envelope === null || !\in_array($envelope, $properties, true)) {
            return null;
        }

        return array_diff($properties, [$envelope, ...$this->registry->config->errorProperties]) === [] ? $envelope : null;
    }

    private function response(MethodDefinition $method, MethodConfig $config, Operation $operation, string $context): void
    {
        $method->unwrap = $config->unwrap;
        $responses = $operation->successResponses();

        foreach ($operation->successContentTypes() as $type) {
            if (\in_array($type, self::RAW_TYPES, true) && $config->response === null) {
                throw new RuntimeException("{$context}: returns {$type} and needs a hand-written method.");
            }
        }

        if ($config->response === 'void') {
            return;
        }

        foreach ($responses as $code => $candidate) {
            if ($candidate !== null && \in_array($candidate->resolvedName(), $this->registry->config->voidResponses, true)) {
                // e.g. Stream's {"success", "message", "statusCode"}: errors arrive as HTTP errors.
                $responses[$code] = null;
            }
        }

        $schema = null;

        if ($config->response !== null) {
            $isList = str_starts_with($config->response, 'list:');
            $schema = $this->componentRef($isList ? substr($config->response, 5) : $config->response);

            if ($isList) {
                $schema = new Schema($schema->spec, ['type' => 'array', 'items' => $schema->node]);
            }
        } else {
            foreach ($responses as $candidate) {
                if ($candidate !== null) {
                    $schema = $candidate;

                    break;
                }
            }

            $method->nullable = $schema !== null && \in_array(null, $responses, true);
        }

        $method->nullable = $method->nullable || $config->nullable;

        if ($schema === null) {
            return;
        }

        if ($method->pagination !== null) {
            $method->returns = $this->pageItemType($schema, $method, $config, $context);

            return;
        }

        $method->unwrap ??= $this->envelopeProperty($schema);

        if ($method->unwrap !== null) {
            $property = $schema->resolve()->properties()[$method->unwrap] ?? throw new RuntimeException("{$context}: response has no property {$method->unwrap} to unwrap.");
            $type = $this->registry->type($property, ($schema->resolvedName() ?? $operation->id) . ".{$method->unwrap}");
        } else {
            $type = $this->registry->type($schema, "{$operation->id}.response");
        }

        $this->registry->markUsage($type, false);
        $method->returns = $type;
    }

    /**
     * The item type of a paginated response: the items property of the page
     * envelope, or the configured item schema when the specification documents
     * the response wrongly.
     */
    private function pageItemType(Schema $schema, MethodDefinition $method, MethodConfig $config, string $context): PhpType
    {
        $pagination = $method->pagination ?? throw new RuntimeException("{$context}: not paginated.");

        if ($config->response !== null) {
            $type = $this->registry->type($schema, "{$method->operation->id}.item");
        } else {
            $items = $schema->resolve()->properties()[$pagination->items] ?? throw new RuntimeException("{$context}: response has no {$pagination->items} property.");
            $list = $this->registry->type($items, ($schema->resolvedName() ?? $method->operation->id) . ".{$pagination->items}");

            if ($list->kind !== PhpType::LIST) {
                throw new RuntimeException("{$context}: {$pagination->items} is not a list.");
            }

            $type = $list->itemOrFail();
        }

        $this->registry->markUsage($type, false);

        return $type;
    }

    private function componentRef(string $name): Schema
    {
        if (!$this->registry->spec->hasSchema($name)) {
            throw new RuntimeException("Unknown schema {$name}.");
        }

        return new Schema($this->registry->spec, ['$ref' => "#/components/schemas/{$name}"]);
    }
}
