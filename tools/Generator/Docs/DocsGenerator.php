<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tools\Generator\Docs;

use BackedEnum;
use GoSuccess\Bunny\Tools\Generator\Analysis;
use LogicException;
use ReflectionClass;
use ReflectionEnum;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use RuntimeException;
use UnitEnum;

/**
 * Renders one Markdown reference page per resource method, plus an index.
 *
 * Everything is read from the resource classes themselves (signatures and
 * docblocks), so hand-written methods are documented the same way as
 * generated ones and the pages cannot drift from the code.
 */
final class DocsGenerator
{
    /**
     * @param array<string, Analysis> $apis     Keyed by API name.
     * @param array<string, string>   $accessors API name => PHP expression reaching its client, e.g. '$bunny->core'.
     */
    public function __construct(
        private readonly array $apis,
        private readonly array $accessors,
    ) {}

    /**
     * @return array<string, string> Relative path under docs/ => content.
     */
    public function render(): array
    {
        $files = [];
        $index = "# API Reference\n\nOne page per resource method. See the [README](../README.md) for an introduction and [examples/](../examples/) for runnable scripts.\n";

        foreach ($this->apis as $name => $analysis) {
            $accessor = $this->accessors[$name] ?? throw new LogicException("No accessor for {$name}.");
            $index .= "\n## {$analysis->config->title}\n\nNamespace `GoSuccess\\Bunny\\{$analysis->config->namespace}`, client `{$analysis->config->client}`, reached via `{$accessor}`.\n";

            foreach ($analysis->resources as $resource) {
                $property = $resource->config->property;

                if (!class_exists($resource->class)) {
                    throw new RuntimeException("{$resource->class} does not exist; run tools/generate.php first.");
                }

                $class = new ReflectionClass($resource->class);
                $index .= "\n### `{$property}`\n\n{$resource->config->description}\n\n";

                foreach ($this->methods($class, array_keys($resource->config->methods), $resource->config->methods) as $method) {
                    $doc = DocBlock::parse((string) $method->getDocComment());
                    $path = "{$name}/{$property}/{$method->getName()}.md";
                    $files[$path] = $this->page($analysis, $accessor, $property, $method, $doc);
                    $summary = $doc->summary === '' ? '' : ' — ' . rtrim($doc->summary, '.');
                    $index .= "- [`{$method->getName()}()`]({$path}){$summary}\n";
                }
            }
        }

        $files['README.md'] = $index;

        return $files;
    }

    /**
     * Public methods in configuration order, each list method followed by its paginator.
     *
     * @param ReflectionClass<object>                                                    $class
     * @param list<string>                                                               $names
     * @param array<string, \GoSuccess\Bunny\Tools\Generator\Config\MethodConfig> $configs
     *
     * @return list<ReflectionMethod>
     */
    private function methods(ReflectionClass $class, array $names, array $configs): array
    {
        $methods = [];

        foreach ($names as $name) {
            $methods[] = $class->getMethod($name);
            $all = $configs[$name]->all;

            if ($all !== null) {
                $methods[] = $class->getMethod($all);
            }
        }

        $documented = array_map(static fn(ReflectionMethod $method): string => $method->getName(), $methods);

        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->getDeclaringClass()->getName() === $class->getName() && !$method->isConstructor() && !\in_array($method->getName(), $documented, true)) {
                throw new RuntimeException("{$class->getName()}::{$method->getName()}() is not listed in the configuration.");
            }
        }

        return $methods;
    }

    private function page(Analysis $analysis, string $accessor, string $property, ReflectionMethod $method, DocBlock $doc): string
    {
        $name = $method->getName();
        $out = "# `{$property}->{$name}()`\n\n";
        $out .= "> {$analysis->config->title}" . ($doc->endpoint !== null ? " · `{$doc->endpoint}`" : '') . "\n\n";

        if ($doc->deprecated) {
            $out .= "> **Deprecated** by bunny.net.\n\n";
        }

        if ($doc->summary !== '') {
            $out .= "{$doc->summary}\n\n";
        }

        foreach ($doc->paragraphs as $paragraph) {
            $out .= "{$paragraph}\n\n";
        }

        $out .= "## Signature\n\n```php\n{$this->signature($method)}\n```\n\n";

        if ($method->getParameters() !== []) {
            $out .= "## Parameters\n\n| Name | Type | Required | Description |\n| --- | --- | --- | --- |\n";

            foreach ($method->getParameters() as $parameter) {
                $type = $doc->params[$parameter->getName()]['type'] ?? $this->typeName($parameter->getType());
                $description = $doc->params[$parameter->getName()]['description'] ?? '';
                $required = $parameter->isOptional() ? 'no' : 'yes';
                $out .= "| `\${$parameter->getName()}` | `" . str_replace('|', '\\|', $type) . "` | {$required} | " . str_replace('|', '\\|', $description) . " |\n";
            }

            $out .= "\n";
        }

        $returns = $doc->return ?? $this->typeName($method->getReturnType());
        $out .= "## Returns\n\n`{$returns}`\n\n";
        $out .= "## Example\n\n```php\n{$this->example($analysis, $accessor, $property, $method)}```\n";

        return $out;
    }

    private function signature(ReflectionMethod $method): string
    {
        $parameters = array_map(fn(ReflectionParameter $parameter): string => $this->parameter($parameter), $method->getParameters());
        $inline = implode(', ', $parameters);
        $return = $this->typeName($method->getReturnType());

        if (\strlen($inline) <= 70) {
            return "public function {$method->getName()}({$inline}): {$return}";
        }

        return "public function {$method->getName()}(\n    " . implode(",\n    ", $parameters) . ",\n): {$return}";
    }

    private function parameter(ReflectionParameter $parameter): string
    {
        $code = $this->typeName($parameter->getType()) . " \${$parameter->getName()}";

        if ($parameter->isDefaultValueAvailable()) {
            $code .= ' = ' . $this->defaultValue($parameter);
        }

        return $code;
    }

    private function defaultValue(ReflectionParameter $parameter): string
    {
        if ($parameter->isDefaultValueConstant()) {
            $constant = (string) $parameter->getDefaultValueConstantName();

            return substr($constant, (int) strrpos($constant, '\\') + ($constant[0] === '\\' ? 1 : 0));
        }

        $value = $parameter->getDefaultValue();

        return match (true) {
            $value === null => 'null',
            \is_bool($value) => $value ? 'true' : 'false',
            \is_int($value), \is_float($value) => (string) $value,
            \is_string($value) => "'{$value}'",
            $value instanceof BackedEnum, $value instanceof UnitEnum => $this->short($value::class) . "::{$value->name}",
            \is_object($value) => 'new ' . $this->short($value::class) . '()',
            default => '[]',
        };
    }

    private function typeName(?ReflectionType $type): string
    {
        if ($type === null) {
            return 'mixed';
        }

        if ($type instanceof ReflectionUnionType) {
            return implode('|', array_map(fn(ReflectionType $inner): string => $this->typeName($inner), $type->getTypes()));
        }

        if (!$type instanceof ReflectionNamedType) {
            return (string) $type;
        }

        $name = $type->isBuiltin() ? $type->getName() : $this->short($type->getName());

        return $type->allowsNull() && $name !== 'mixed' && $name !== 'null' ? "?{$name}" : $name;
    }

    private function example(Analysis $analysis, string $accessor, string $property, ReflectionMethod $method): string
    {
        $uses = [];
        $arguments = [];

        foreach ($method->getParameters() as $parameter) {
            if ($parameter->isOptional()) {
                continue;
            }

            [$value, $class] = $this->sampleArgument($parameter);
            $arguments[] = "{$parameter->getName()}: {$value}";

            if ($class !== null) {
                $uses[] = $class;
            }
        }

        $call = "{$accessor}->{$property}->{$method->getName()}(" . implode(', ', $arguments) . ')';
        $returns = $this->typeName($method->getReturnType());
        $statement = match (true) {
            $returns === 'void' => "{$call};\n",
            str_ends_with($returns, 'Paginator') => "foreach ({$call} as \$item) {\n    // ...\n}\n",
            default => "\$result = {$call};\n",
        };

        $uses = array_unique(['GoSuccess\\Bunny\\Bunny', ...$uses]);
        sort($uses);
        $useBlock = implode('', array_map(static fn(string $class): string => "use {$class};\n", $uses));

        return "{$useBlock}\n\$bunny = new Bunny('your-api-key');\n\n{$statement}";
    }

    /**
     * @return array{string, string|null} The argument code and a class to import.
     */
    private function sampleArgument(ReflectionParameter $parameter): array
    {
        $type = $parameter->getType();
        $name = strtolower($parameter->getName());
        $types = $type instanceof ReflectionUnionType ? $type->getTypes() : ($type === null ? [] : [$type]);
        $first = $types[0] ?? null;
        $typeName = $first instanceof ReflectionNamedType ? $first->getName() : 'mixed';

        if ($first instanceof ReflectionNamedType && !$first->isBuiltin()) {
            if (enum_exists($typeName)) {
                $case = new ReflectionEnum($typeName)->getCases()[0] ?? null;

                return [$this->short($typeName) . '::' . ($case?->getName() ?? 'Value'), $typeName];
            }

            if ($typeName === 'DateTimeInterface' || $typeName === 'DateTimeImmutable') {
                return ["new DateTimeImmutable('-7 days')", 'DateTimeImmutable'];
            }

            if ($typeName === 'GoSuccess\\Bunny\\Http\\Stream') {
                return ["Stream::fromFile('path/to/file')", $typeName];
            }

            return ['new ' . $this->short($typeName) . '(/* ... */)', $typeName];
        }

        return [match ($typeName) {
            'int' => '123',
            'float' => '1.5',
            'bool' => 'true',
            'array' => '[/* ... */]',
            default => match (true) {
                str_contains($name, 'hostname') || str_contains($name, 'domain') => "'cdn.example.com'",
                str_contains($name, 'url') => "'https://example.com/'",
                str_contains($name, 'ip') => "'203.0.113.10'",
                str_contains($name, 'guid') || str_ends_with($name, 'id') => "'00000000-0000-0000-0000-000000000000'",
                default => "'example'",
            },
        }, null];
    }

    private function short(string $class): string
    {
        return substr($class, (int) strrpos($class, '\\') + (str_contains($class, '\\') ? 1 : 0));
    }
}
