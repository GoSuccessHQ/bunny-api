<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tools\Generator\Writer;

use GoSuccess\Bunny\Tools\Generator\Definition\ModelDefinition;
use GoSuccess\Bunny\Tools\Generator\Definition\PropertyDefinition;
use GoSuccess\Bunny\Tools\Generator\PhpType;
use GoSuccess\Bunny\Tools\Generator\Registry;
use LogicException;

/**
 * Renders model classes.
 *
 * Three shapes exist, depending on where a model is used:
 *
 * - response only: promoted readonly properties with plain types and fromArray();
 * - request only: promoted readonly properties typed `T|Undefined(|null)`, so a
 *   payload holds exactly what the caller passed, and toArray();
 * - both: plain-typed properties for reading plus a record of which fields were
 *   provided, so toArray() sends only those (partial updates).
 *
 * Reading rules: lists and maps are never null; enums and dates are always
 * nullable (an unknown case or an unparsable date must not break a response);
 * other values follow the specification's nullability and fall back to their
 * zero value when the API omits a field the specification promises.
 */
final class ModelWriter
{
    private const string CAST = 'GoSuccess\\Bunny\\Model\\Cast';
    private const string UNDEFINED = 'GoSuccess\\Bunny\\Model\\Undefined';
    private const string JSON = 'GoSuccess\\Bunny\\Model\\Json';

    public function __construct(private readonly Registry $registry) {}

    public function render(ModelDefinition $model, string $source): string
    {
        $namespace = substr($model->class, 0, (int) strrpos($model->class, '\\'));
        $short = substr($model->class, (int) strrpos($model->class, '\\') + 1);
        $file = new CodeFile($namespace, $short);

        $body = match (true) {
            $model->request && $model->response => $this->shared($model, $file, $short),
            $model->request => $this->requestOnly($model, $file, $short),
            default => $this->responseOnly($model, $file, $short),
        };

        return $file->render($body, $source);
    }

    private function responseOnly(ModelDefinition $model, CodeFile $file, string $short): string
    {
        $interface = $file->alias('GoSuccess\\Bunny\\Model\\ResponseModel');
        $parameters = '';
        $arguments = '';

        foreach ($model->properties as $property) {
            $type = $this->readType($property, $model, $file);
            $parameters .= $this->propertyDoc($property, $type, '        ');
            $parameters .= "        public {$type['native']} \${$property->phpName} = {$type['default']},\n";
            $arguments .= "            {$property->phpName}: {$this->readExpression($property, $model, $file)},\n";
        }

        $constructor = $parameters === '' ? "    public function __construct() {}\n" : "    public function __construct(\n{$parameters}    ) {}\n";
        $fromArray = $arguments === ''
            ? "    public static function fromArray(array \$data): static\n    {\n        return new self();\n    }\n"
            : "    public static function fromArray(array \$data): static\n    {\n        return new self(\n{$arguments}        );\n    }\n";

        return $this->classDoc($model) . "final readonly class {$short} implements {$interface}\n{\n{$constructor}\n{$fromArray}}\n";
    }

    private function requestOnly(ModelDefinition $model, CodeFile $file, string $short): string
    {
        $interface = $file->alias('GoSuccess\\Bunny\\Model\\RequestModel');
        $undefined = $file->alias(self::UNDEFINED);
        $properties = array_values(array_filter($model->properties, static fn(PropertyDefinition $property): bool => !$property->readOnly));
        // PHP requires required parameters before optional ones.
        usort($properties, static fn(PropertyDefinition $a, PropertyDefinition $b): int => (int) $b->required <=> (int) $a->required);

        $parameters = '';
        $body = '';

        foreach ($properties as $property) {
            $type = $this->writeType($property, $file, !$property->required);
            $parameters .= $this->propertyDoc($property, $type, '        ');
            $default = $property->required ? '' : " = {$undefined}::Value";
            $parameters .= "        public {$type['native']} \${$property->phpName}{$default},\n";

            $value = $this->serialize($property->type, "\$this->{$property->phpName}", $this->writeNullable($property), $file);
            $assignment = "\$data['{$this->escape($property->jsonName)}'] = {$value};";
            $body .= $property->required
                ? "        {$assignment}\n"
                : "\n        if (!\$this->{$property->phpName} instanceof {$undefined}) {\n            {$assignment}\n        }\n";
        }

        $constructor = $parameters === '' ? "    public function __construct() {}\n" : "    public function __construct(\n{$parameters}    ) {}\n";
        $toArray = $body === ''
            ? "    public function toArray(): array\n    {\n        return [];\n    }\n"
            : "    public function toArray(): array\n    {\n        \$data = [];\n{$body}\n        return \$data;\n    }\n";

        return $this->classDoc($model) . "final readonly class {$short} implements {$interface}\n{\n{$constructor}\n{$toArray}}\n";
    }

    private function shared(ModelDefinition $model, CodeFile $file, string $short): string
    {
        $request = $file->alias('GoSuccess\\Bunny\\Model\\RequestModel');
        $response = $file->alias('GoSuccess\\Bunny\\Model\\ResponseModel');
        $undefined = $file->alias(self::UNDEFINED);

        $declarations = '';
        $parameters = '';
        $paramDocs = [];
        $assignments = '';
        $provided = '';
        $arguments = '';
        $serialization = '';

        foreach ($model->properties as $property) {
            $read = $this->readType($property, $model, $file);
            $declarations .= $this->propertyDoc($property, $read, '    ');
            $declarations .= "    public {$read['native']} \${$property->phpName};\n\n";

            $write = $this->writeType($property, $file, true);
            $parameters .= "        {$write['native']} \${$property->phpName} = {$undefined}::Value,\n";

            if ($write['doc'] !== null) {
                $paramDocs[] = "@param {$write['doc']} \${$property->phpName}";
            }

            $name = $property->phpName;
            $assignments .= "        \$this->{$name} = \${$name} instanceof {$undefined} ? {$read['default']} : \${$name};\n";

            $key = $this->escape($property->jsonName);

            if (!$property->readOnly) {
                $provided .= "            '{$key}' => !\${$name} instanceof {$undefined},\n";
                $value = $this->serialize($property->type, "\$this->{$name}", $read['nullable'], $file);
                $serialization .= "\n        if (isset(\$this->provided['{$key}'])) {\n            \$data['{$key}'] = {$value};\n        }\n";
            }

            $read = $this->readExpression($property, $model, $file, raw: true);
            $arguments .= $property->type->isCollection()
                ? "            {$name}: {$read} ?: {$undefined}::Value,\n"
                : "            {$name}: {$read} ?? {$undefined}::Value,\n";
        }

        $constructorDoc = $paramDocs === [] ? '' : Doc::block([$paramDocs], '    ');
        $providedDoc = Doc::block([['Payload keys the caller provided; toArray() sends exactly these.'], ['@var array<string, true>']], '    ');
        $providedArray = $provided === '' ? '[]' : "array_filter([\n{$provided}        ])";

        return $this->classDoc($model)
            . "final readonly class {$short} implements {$request}, {$response}\n{\n"
            . $declarations
            . $providedDoc . "    private array \$provided;\n\n"
            . $constructorDoc
            . ($parameters === '' ? "    public function __construct()\n    {\n" : "    public function __construct(\n{$parameters}    ) {\n")
            . $assignments
            . "        \$this->provided = {$providedArray};\n    }\n\n"
            . "    public static function fromArray(array \$data): static\n    {\n        return new self(\n{$arguments}        );\n    }\n\n"
            . "    public function toArray(): array\n    {\n        \$data = [];\n{$serialization}\n        return \$data;\n    }\n}\n";
    }

    /**
     * @return array{native: string, doc: string|null, default: string, nullable: bool}
     */
    private function readType(PropertyDefinition $property, ModelDefinition $model, CodeFile $file): array
    {
        $type = $property->type;
        $alias = $file->alias(...);

        if ($type->isCollection()) {
            return ['native' => 'array', 'doc' => $type->doc($alias), 'default' => '[]', 'nullable' => false];
        }

        $nullable = $this->isNullableRead($property, $model);
        $native = $type->native($alias);

        if ($type->kind === PhpType::MIXED) {
            return ['native' => 'mixed', 'doc' => null, 'default' => 'null', 'nullable' => true];
        }

        if ($nullable) {
            return [
                'native' => "?{$native}",
                'doc' => $type->needsDoc() ? $type->doc($alias) . '|null' : null,
                'default' => 'null',
                'nullable' => true,
            ];
        }

        $default = match ($type->kind) {
            PhpType::STRING => "''",
            PhpType::INT => '0',
            PhpType::FLOAT => '0.0',
            PhpType::BOOL => 'false',
            PhpType::OBJECT => '[]',
            PhpType::MODEL => "new {$native}()",
            default => throw new LogicException("No zero value for {$type->kind}."),
        };

        return ['native' => $native, 'doc' => $type->needsDoc() ? $type->doc($alias) : null, 'default' => $default, 'nullable' => false];
    }

    private function isNullableRead(PropertyDefinition $property, ModelDefinition $model): bool
    {
        return match ($property->type->kind) {
            PhpType::ENUM, PhpType::DATE, PhpType::MIXED => true,
            PhpType::MODEL => $property->nullable || $this->reaches($property->type->classOrFail(), $model->class, []),
            default => $property->nullable,
        };
    }

    /**
     * Whether $from can reach $target through non-nullable model properties;
     * such a cycle must be broken with a nullable property.
     *
     * @param array<string, true> $seen
     */
    private function reaches(string $from, string $target, array $seen): bool
    {
        if ($from === $target) {
            return true;
        }

        if (isset($seen[$from])) {
            return false;
        }

        $seen[$from] = true;

        foreach ($this->registry->models[$from]->properties as $property) {
            if ($property->type->kind === PhpType::MODEL && !$property->nullable && $this->reaches($property->type->classOrFail(), $target, $seen)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{native: string, doc: string|null}
     */
    private function writeType(PropertyDefinition $property, CodeFile $file, bool $optional): array
    {
        $type = $property->type;
        $alias = $file->alias(...);
        $undefined = $optional ? '|' . $file->alias(self::UNDEFINED) : '';
        $nullable = $this->writeNullable($property);

        if ($type->kind === PhpType::MIXED) {
            return ['native' => 'mixed', 'doc' => null];
        }

        $native = $type->native($alias, true) . $undefined . ($nullable ? '|null' : '');
        $doc = $type->needsDoc() ? $type->doc($alias, true) . $undefined . ($nullable ? '|null' : '') : null;

        if (!$optional && $nullable) {
            $native = '?' . $type->native($alias, true);
        }

        return ['native' => $native, 'doc' => $doc];
    }

    /**
     * Lists and maps are cleared with an empty value rather than null.
     */
    private function writeNullable(PropertyDefinition $property): bool
    {
        return $property->nullable && !$property->type->isCollection() && $property->type->kind !== PhpType::MIXED;
    }

    private function readExpression(PropertyDefinition $property, ModelDefinition $model, CodeFile $file, bool $raw = false): string
    {
        $cast = $file->alias(self::CAST);
        $input = "\$data['{$this->escape($property->jsonName)}'] ?? null";
        $type = $property->type;
        $alias = $file->alias(...);

        $expression = match ($type->kind) {
            PhpType::STRING => "{$cast}::string({$input})",
            PhpType::INT => "{$cast}::int({$input})",
            PhpType::FLOAT => "{$cast}::float({$input})",
            PhpType::BOOL => "{$cast}::bool({$input})",
            PhpType::DATE => "{$cast}::dateTime({$input})",
            PhpType::ENUM => "{$cast}::{$type->backing}Enum({$alias($type->classOrFail())}::class, {$input})",
            PhpType::MODEL => "{$cast}::model({$alias($type->classOrFail())}::class, {$input})",
            PhpType::LIST => $type->itemOrFail()->kind === PhpType::MODEL
                ? "{$cast}::modelList({$alias($type->itemOrFail()->classOrFail())}::class, {$input})"
                : "{$cast}::listOf({$input}, {$this->converter($type->itemOrFail(), $file)})",
            PhpType::MAP => "{$cast}::mapOf({$input}, {$this->converter($type->itemOrFail(), $file)})",
            PhpType::OBJECT => "{$cast}::object({$input})",
            PhpType::MIXED => "\$data['{$this->escape($property->jsonName)}'] ?? null",
            default => throw new LogicException("Unknown kind {$type->kind}."),
        };

        if ($raw || $type->isCollection() || $type->kind === PhpType::MIXED) {
            return $expression;
        }

        $read = $this->readType($property, $model, $file);

        return $read['nullable'] ? $expression : "{$expression} ?? {$read['default']}";
    }

    /**
     * A closure expression converting one element: Closure(mixed): ?T.
     */
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
            default => throw new LogicException("Unknown kind {$type->kind}."),
        };
    }

    /**
     * An expression turning a PHP value into its JSON payload form.
     */
    private function serialize(PhpType $type, string $expression, bool $nullable, CodeFile $file): string
    {
        $safe = $nullable ? '?->' : '->';

        return match ($type->kind) {
            PhpType::STRING, PhpType::INT, PhpType::FLOAT, PhpType::BOOL, PhpType::MIXED => $expression,
            PhpType::ENUM => "{$expression}{$safe}value",
            PhpType::MODEL => "{$expression}{$safe}toArray()",
            PhpType::DATE => $nullable
                ? "{$expression} === null ? null : {$file->alias(self::JSON)}::date({$expression})"
                : "{$file->alias(self::JSON)}::date({$expression})",
            PhpType::OBJECT => $nullable
                ? "{$expression} === null ? null : {$file->alias(self::JSON)}::map({$expression})"
                : "{$file->alias(self::JSON)}::map({$expression})",
            PhpType::LIST => $this->needsMapping($type->itemOrFail())
                ? "array_map({$this->serializer($type->itemOrFail(), $file)}, {$expression})"
                : $expression,
            PhpType::MAP => $this->needsMapping($type->itemOrFail())
                ? "{$file->alias(self::JSON)}::map(array_map({$this->serializer($type->itemOrFail(), $file)}, {$expression}))"
                : "{$file->alias(self::JSON)}::map({$expression})",
            default => throw new LogicException("Unknown kind {$type->kind}."),
        };
    }

    private function needsMapping(PhpType $item): bool
    {
        return !$item->isScalar() && $item->kind !== PhpType::MIXED;
    }

    /**
     * A closure serializing one element of a list or map.
     */
    private function serializer(PhpType $item, CodeFile $file): string
    {
        $alias = $file->alias(...);

        return match ($item->kind) {
            PhpType::ENUM => "static fn({$alias($item->classOrFail())} \$item): {$item->backing} => \$item->value",
            PhpType::MODEL => "static fn({$alias($item->classOrFail())} \$item): array => \$item->toArray()",
            PhpType::DATE => "{$file->alias(self::JSON)}::date(...)",
            default => "static fn(mixed \$item): mixed => {$this->serialize($item, '$item', false, $file)}",
        };
    }

    /**
     * @param array{native: string, doc: string|null} $type
     */
    private function propertyDoc(PropertyDefinition $property, array $type, string $indent): string
    {
        $tags = [];

        if ($type['doc'] !== null) {
            $tags[] = "@var {$type['doc']}";
        }

        if ($property->deprecated) {
            $tags[] = '@deprecated';
        }

        return Doc::block([Doc::lines($property->description), $tags], $indent);
    }

    private function classDoc(ModelDefinition $model): string
    {
        $tags = [];

        if ($model->deprecated) {
            $tags[] = '@deprecated';
        }

        return Doc::block([Doc::lines($model->description), ["Schema: {$model->source}"], $tags]);
    }

    private function escape(string $key): string
    {
        return str_replace(['\\', "'"], ['\\\\', "\\'"], $key);
    }
}
