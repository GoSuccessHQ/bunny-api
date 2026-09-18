<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tools\Generator\Writer;

use GoSuccess\Bunny\Tools\Generator\Config\ApiConfig;
use GoSuccess\Bunny\Tools\Generator\Definition\MethodDefinition;
use GoSuccess\Bunny\Tools\Generator\Definition\ParameterDefinition;
use GoSuccess\Bunny\Tools\Generator\Definition\ResourceDefinition;
use GoSuccess\Bunny\Tools\Generator\PhpType;
use LogicException;

/**
 * Renders resource classes.
 */
final class ResourceWriter
{
    private const string CAST = 'GoSuccess\\Bunny\\Model\\Cast';
    private const string JSON = 'GoSuccess\\Bunny\\Model\\Json';
    private const string METHOD = 'GoSuccess\\Bunny\\Http\\Method';
    private const string PAGE = 'GoSuccess\\Bunny\\Pagination\\Page';
    private const string PAGINATOR = 'GoSuccess\\Bunny\\Pagination\\Paginator';

    public function __construct(private readonly ApiConfig $config) {}

    public function render(ResourceDefinition $resource, string $source): string
    {
        $namespace = substr($resource->class, 0, (int) strrpos($resource->class, '\\'));
        $short = $resource->config->class;
        $file = new CodeFile($namespace, $short);
        $base = $file->alias('GoSuccess\\Bunny\\Resource\\AbstractResource');

        $methods = [];

        foreach ($resource->methods as $method) {
            $methods[] = $method->pagination !== null ? $this->paginated($method, $file) : $this->regular($method, $file);
        }

        $trait = '';

        if ($resource->config->handwritten) {
            $trait = '    use ' . $file->alias("{$namespace}\\Handwritten\\{$resource->config->traitName()}") . ";\n\n";
        }

        $doc = Doc::block([[$resource->config->description]]);
        $body = $trait . $this->constructor($file) . implode("\n", $methods);

        return $file->render("{$doc}final class {$short} extends {$base}\n{\n" . rtrim($body, "\n") . "\n}\n", $source);
    }

    /**
     * Resources of an API with client parameters receive them from the client.
     */
    private function constructor(CodeFile $file): string
    {
        if ($this->config->clientParameters === []) {
            return '';
        }

        $connection = $file->alias('GoSuccess\\Bunny\\Http\\Connection');
        $parameters = [];

        foreach ($this->config->clientParameters as $name => $parameter) {
            $parameters[] = "private readonly {$parameter['type']} \${$name}";
        }

        return "    /**\n     * @internal Created by the client.\n     */\n"
            . "    public function __construct({$connection} \$connection, " . implode(', ', $parameters) . ")\n"
            . "    {\n        parent::__construct(\$connection);\n    }\n\n";
    }

    private function regular(MethodDefinition $method, CodeFile $file): string
    {
        $returns = $this->returnType($method, $file);
        $call = $this->call($method, $file, $method->parameters);
        $body = $this->bodyStatements($method, $file);

        if ($method->returns === null) {
            $code = "{$body}        {$call};\n";
        } else {
            $read = $this->read($method->returns, '$data', $method->unwrap, $file);

            if ($method->nullable) {
                $read = "\$data === null ? null : {$read}";
            }

            $code = "{$body}        \$data = {$call};\n\n        return {$read};\n";
        }

        $tags = $returns['doc'] !== null ? ["@return {$returns['doc']}"] : [];

        return $this->docBlock($method, $method->parameters, $file, $tags)
            . $this->deprecation($method)
            . "    public function {$method->config->name}({$this->signature($method->parameters, $file)}): {$returns['native']}\n    {\n{$code}    }\n";
    }

    private function paginated(MethodDefinition $method, CodeFile $file): string
    {
        $pagination = $method->pagination ?? throw new LogicException('Not paginated.');
        $itemType = $method->returns ?? throw new LogicException("{$method->operation->id}: paginated method without item type.");
        $page = $file->alias(self::PAGE);
        $itemDoc = $itemType->doc($file->alias(...));

        [$class, $function] = explode('::', $pagination->factory, 2);
        $factory = $file->alias($this->config->referencedClass($class)) . "::{$function}";

        $call = $this->call($method, $file, $method->parameters);
        $items = $this->read(new PhpType(PhpType::LIST, item: $itemType), "\$data['{$pagination->items}'] ?? null", null, $file);
        $object = "self::expectObject({$call})";

        $list = $this->docBlock($method, $method->parameters, $file, ["@return {$page}<{$itemDoc}>"])
            . $this->deprecation($method)
            . "    public function {$method->config->name}({$this->signature($method->parameters, $file)}): {$page}\n    {\n"
            . $this->bodyStatements($method, $file)
            . "        \$data = {$object};\n\n        return {$factory}(\$data, {$items});\n    }\n";

        if ($method->config->all === null) {
            return $list;
        }

        // The paginator method takes the same filters, but no position.
        $paginator = $file->alias(self::PAGINATOR);
        $filters = [];
        $size = null;

        foreach (self::exposed($method->parameters) as $parameter) {
            if ($parameter->location === ParameterDefinition::QUERY && $parameter->specName === $pagination->position) {
                continue;
            }

            if ($parameter->location === ParameterDefinition::QUERY && $parameter->specName === $pagination->size && ($pagination->allSize ?? $pagination->pageSize) !== null) {
                $size = new ParameterDefinition(
                    $parameter->specName,
                    $parameter->phpName,
                    $parameter->type,
                    $parameter->location,
                    false,
                    (string) ($pagination->allSize ?? $pagination->pageSize),
                    $parameter->description,
                );

                continue;
            }

            $filters[] = $parameter;
        }

        $allParameters = $size === null ? $filters : [...$filters, $size];
        $arguments = [];

        foreach (self::exposed($method->parameters) as $parameter) {
            $arguments[] = $parameter->location === ParameterDefinition::QUERY && $parameter->specName === $pagination->position
                ? "{$parameter->phpName}: " . ($pagination->positionType === 'int'
                    ? '\\is_int($position) ? $position : ' . ($pagination->first ?? 1)
                    : '\\is_string($position) ? $position : null')
                : "{$parameter->phpName}: \${$parameter->phpName}";
        }

        $summary = "Iterate lazily over every item of {$method->config->name}(), across all pages.";
        $doc = $this->docBlock($method, $allParameters, $file, ["@return {$paginator}<{$itemDoc}>"], $summary);

        $all = $doc
            . "    public function {$method->config->all}({$this->signature($allParameters, $file)}): {$paginator}\n    {\n"
            . "        return new {$paginator}(fn(int|string|null \$position): {$page} => \$this->{$method->config->name}(\n"
            . implode('', array_map(static fn(string $argument): string => "            {$argument},\n", $arguments))
            . "        ));\n    }\n";

        return "{$list}\n{$all}";
    }

    /**
     * Statements preparing a flattened request body.
     */
    private function bodyStatements(MethodDefinition $method, CodeFile $file): string
    {
        $fields = [...$method->parametersIn(ParameterDefinition::BODY), ...$method->parametersIn(ParameterDefinition::BOUND)];

        if ($fields === []) {
            return '';
        }

        $required = [];
        $optional = '';

        foreach ($fields as $field) {
            $key = $this->escape($field->specName);
            $value = $this->serialize($field->type, "\${$field->phpName}", $field->nullable && $field->isRequired(), $file);

            if ($field->isRequired()) {
                $required[] = "'{$key}' => {$value}";
            } else {
                $optional .= "\n        if (\${$field->phpName} !== null) {\n            \$body['{$key}'] = {$this->serialize($field->type, "\${$field->phpName}", false, $file)};\n        }\n";
            }
        }

        $initial = $required === [] ? '[]' : "[\n" . implode('', array_map(static fn(string $pair): string => "            {$pair},\n", $required)) . '        ]';

        return "        \$body = {$initial};\n{$optional}\n";
    }

    /**
     * The connection call, e.g. `$this->connection->json(Method::Get, "pullzone/{$id}", [...])`.
     *
     * @param list<ParameterDefinition> $parameters
     */
    private function call(MethodDefinition $method, CodeFile $file, array $parameters): string
    {
        $httpMethod = $file->alias(self::METHOD) . '::' . ucfirst(strtolower($method->operation->method));
        $arguments = [$httpMethod, $this->path($method)];

        $query = array_values(array_filter($parameters, static fn(ParameterDefinition $parameter): bool => $parameter->location === ParameterDefinition::QUERY));
        $hasQuery = $query !== [];

        if ($hasQuery) {
            $pairs = array_map(static fn(ParameterDefinition $parameter): string => "            '{$parameter->specName}' => \${$parameter->phpName},\n", $query);
            $arguments[] = "[\n" . implode('', $pairs) . '        ]';
        }

        $payload = $method->payload();

        if ($payload !== null) {
            $value = $payload->type->kind === PhpType::MAP
                ? "\${$payload->phpName}"
                : ($payload->nullable ? "\${$payload->phpName}?->toArray()" : "\${$payload->phpName}->toArray()");
            $arguments[] = $hasQuery ? $value : "body: {$value}";
        } elseif ($method->parametersIn(ParameterDefinition::BODY) !== [] || $method->parametersIn(ParameterDefinition::BOUND) !== []) {
            $arguments[] = $hasQuery ? '$body' : 'body: $body';
        }

        $inline = implode(', ', $arguments);

        return "\$this->connection->json({$inline})";
    }

    private function path(MethodDefinition $method): string
    {
        $path = ltrim($method->operation->path, '/');
        $byName = [];

        foreach ([...$method->parametersIn(ParameterDefinition::PATH), ...$method->parametersIn(ParameterDefinition::CLIENT)] as $parameter) {
            $byName[$parameter->specName] = $parameter;
        }

        if ($byName === []) {
            return "'{$this->escape($path)}'";
        }

        $interpolated = preg_replace_callback('/\{([^}]+)\}/', static function (array $match) use ($byName): string {
            $parameter = $byName[$match[1]] ?? throw new LogicException("Unknown placeholder {$match[1]}.");

            $variable = $parameter->location === ParameterDefinition::CLIENT ? "\$this->{$parameter->phpName}" : "\${$parameter->phpName}";

            return $parameter->type->kind === PhpType::INT ? "{{$variable}}" : "{\$this->segment({$variable})}";
        }, str_replace(['\\', '"', '$'], ['\\\\', '\\"', '\\$'], $path));

        return "\"{$interpolated}\"";
    }

    /**
     * @param list<ParameterDefinition> $parameters
     */
    private function signature(array $parameters, CodeFile $file): string
    {
        $parameters = self::exposed($parameters);

        if ($parameters === []) {
            return '';
        }

        $parts = [];

        foreach ($parameters as $parameter) {
            $type = $this->parameterType($parameter, $file);
            $default = $parameter->default === null ? '' : " = {$parameter->default}";
            $parts[] = "{$type} \${$parameter->phpName}{$default}";
        }

        $inline = implode(', ', $parts);

        if (\strlen($inline) <= 80) {
            return $inline;
        }

        return "\n" . implode('', array_map(static fn(string $part): string => "        {$part},\n", $parts)) . '    ';
    }

    private function parameterType(ParameterDefinition $parameter, CodeFile $file): string
    {
        $native = $parameter->type->native($file->alias(...), true);

        return $parameter->nullable && $native !== 'mixed' ? "?{$native}" : $native;
    }

    /**
     * @return array{native: string, doc: string|null}
     */
    private function returnType(MethodDefinition $method, CodeFile $file): array
    {
        if ($method->returns === null) {
            return ['native' => 'void', 'doc' => null];
        }

        $type = $method->returns;
        $alias = $file->alias(...);
        $native = $type->native($alias);
        $nullable = $method->nullable && $native !== 'mixed';

        return [
            'native' => $nullable ? "?{$native}" : $native,
            'doc' => $type->needsDoc() ? $type->doc($alias) . ($nullable ? '|null' : '') : null,
        ];
    }

    /**
     * Expression that reads a decoded response ($data) as the given type.
     */
    private function read(PhpType $type, string $input, ?string $unwrap, CodeFile $file): string
    {
        $cast = $file->alias(self::CAST);
        $alias = $file->alias(...);

        if ($unwrap !== null) {
            $input = "self::expectObject({$input})['{$this->escape($unwrap)}'] ?? null";
        }

        return match ($type->kind) {
            PhpType::MODEL => "self::toModel({$alias($type->classOrFail())}::class, {$input})",
            PhpType::LIST => $type->itemOrFail()->kind === PhpType::MODEL
                ? ($input === '$data'
                    ? "self::toModelList({$alias($type->itemOrFail()->classOrFail())}::class, {$input})"
                    : "{$cast}::modelList({$alias($type->itemOrFail()->classOrFail())}::class, {$input})")
                : "{$cast}::listOf({$input}, {$this->converter($type->itemOrFail(), $file)})",
            PhpType::MAP => "{$cast}::mapOf({$input}, {$this->converter($type->itemOrFail(), $file)})",
            PhpType::STRING => "{$cast}::string({$input}) ?? ''",
            PhpType::INT => "{$cast}::int({$input}) ?? 0",
            PhpType::FLOAT => "{$cast}::float({$input}) ?? 0.0",
            PhpType::BOOL => "{$cast}::bool({$input}) ?? false",
            PhpType::OBJECT => "self::expectObject({$input})",
            PhpType::MIXED => $input,
            default => throw new LogicException("Unsupported response type {$type->kind}."),
        };
    }

    private function converter(PhpType $type, CodeFile $file): string
    {
        $cast = $file->alias(self::CAST);
        $alias = $file->alias(...);

        return match ($type->kind) {
            PhpType::STRING => "{$cast}::string(...)",
            PhpType::INT => "{$cast}::int(...)",
            PhpType::FLOAT => "{$cast}::float(...)",
            PhpType::BOOL => "{$cast}::bool(...)",
            PhpType::DATE => "{$cast}::dateTime(...)",
            PhpType::OBJECT => "{$cast}::object(...)",
            PhpType::ENUM => "static fn(mixed \$value): ?{$alias($type->classOrFail())} => {$cast}::{$type->backing}Enum({$alias($type->classOrFail())}::class, \$value)",
            PhpType::MODEL => "static fn(mixed \$value): ?{$alias($type->classOrFail())} => {$cast}::model({$alias($type->classOrFail())}::class, \$value)",
            PhpType::LIST => $type->itemOrFail()->kind === PhpType::MODEL
                ? "static fn(mixed \$value): array => {$cast}::modelList({$alias($type->itemOrFail()->classOrFail())}::class, \$value)"
                : "static fn(mixed \$value): array => {$cast}::listOf(\$value, {$this->converter($type->itemOrFail(), $file)})",
            PhpType::MAP => "static fn(mixed \$value): array => {$cast}::mapOf(\$value, {$this->converter($type->itemOrFail(), $file)})",
            PhpType::MIXED => 'static fn(mixed $value): mixed => $value',
            default => throw new LogicException("Unsupported element type {$type->kind}."),
        };
    }

    private function serialize(PhpType $type, string $expression, bool $nullable, CodeFile $file): string
    {
        $safe = $nullable ? '?->' : '->';

        return match ($type->kind) {
            PhpType::ENUM => "{$expression}{$safe}value",
            PhpType::MODEL => "{$expression}{$safe}toArray()",
            PhpType::DATE => "{$file->alias(self::JSON)}::date({$expression})",
            PhpType::MAP, PhpType::OBJECT => "{$file->alias(self::JSON)}::map({$expression})",
            PhpType::LIST => match ($type->itemOrFail()->kind) {
                PhpType::MODEL => "array_map(static fn({$file->alias($type->itemOrFail()->classOrFail())} \$item): array => \$item->toArray(), {$expression})",
                PhpType::ENUM => "array_map(static fn({$file->alias($type->itemOrFail()->classOrFail())} \$item): {$type->itemOrFail()->backing} => \$item->value, {$expression})",
                default => $expression,
            },
            default => $expression,
        };
    }

    /**
     * @param list<ParameterDefinition> $parameters
     * @param list<string>              $tags
     */
    private function docBlock(MethodDefinition $method, array $parameters, CodeFile $file, array $tags, ?string $summary = null): string
    {
        $operation = $method->operation;
        $summaryLines = $summary !== null ? [$summary] : Doc::lines($operation->summary());
        $description = $summary !== null ? [] : Doc::lines($operation->description());

        if ($description === $summaryLines) {
            $description = [];
        }

        $endpoint = ["`{$operation->method} {$operation->path}`"];
        $note = Doc::lines($method->config->note);
        $parameters = self::exposed($parameters);

        $paramLines = [];
        $types = [];

        foreach ($parameters as $parameter) {
            $types[] = $this->parameterDocType($parameter, $file);
        }

        $width = $types === [] ? 0 : max(array_map('strlen', $types));
        $nameWidth = $parameters === [] ? 0 : max(array_map(static fn(ParameterDefinition $parameter): int => \strlen($parameter->phpName) + 1, $parameters));

        foreach ($parameters as $index => $parameter) {
            $text = $parameter->description === null ? '' : ' ' . str_replace("\n", ' ', trim($parameter->description));
            $paramLines[] = rtrim('@param ' . str_pad($types[$index], $width) . ' ' . str_pad("\${$parameter->phpName}", $nameWidth) . $text);
        }

        if ($method->operation->isDeprecated()) {
            $tags[] = '@deprecated';
        }

        return Doc::block([$summaryLines, $description, $note, $endpoint, $paramLines, $tags], '    ');
    }

    /**
     * The parameters a caller passes; client parameters come from the client
     * and bound body properties from their path parameter.
     *
     * @param list<ParameterDefinition> $parameters
     *
     * @return list<ParameterDefinition>
     */
    private static function exposed(array $parameters): array
    {
        return array_values(array_filter(
            $parameters,
            static fn(ParameterDefinition $parameter): bool => !\in_array($parameter->location, [ParameterDefinition::CLIENT, ParameterDefinition::BOUND], true),
        ));
    }

    private function parameterDocType(ParameterDefinition $parameter, CodeFile $file): string
    {
        $alias = $file->alias(...);
        $doc = $parameter->type->doc($alias, true);

        return $parameter->nullable && $doc !== 'mixed' ? "{$doc}|null" : $doc;
    }

    private function deprecation(MethodDefinition $method): string
    {
        if (!$method->operation->isDeprecated()) {
            return '';
        }

        return "    #[\\Deprecated('This endpoint is deprecated by bunny.net.')]\n";
    }

    private function escape(string $value): string
    {
        return str_replace(['\\', "'"], ['\\\\', "\\'"], $value);
    }
}
