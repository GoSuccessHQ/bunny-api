<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tools\Generator;

use GoSuccess\Bunny\Tools\Generator\Definition\EnumCase;
use GoSuccess\Bunny\Tools\Generator\Definition\EnumDefinition;
use RuntimeException;

/**
 * Builds enum definitions and resolves their case names.
 *
 * The specifications name enum values in four different ways, and not all of
 * them are reliable (Shield's `x-enum-varnames` lists more names than values).
 * Names are therefore taken, in this order, from:
 *
 * 1. the configuration (tools/config/<api>.php),
 * 2. "<value> = <Name>" lines in the description (NSwag writes them for every
 *    enum and they pair each value with its name explicitly),
 * 3. `x-enumNames`, if it has exactly one name per value,
 * 4. the `title` of each `oneOf` variant (Stream),
 * 5. the value itself, for string enums.
 *
 * When two sources disagree, generation fails instead of guessing.
 */
final class EnumBuilder
{
    /** @var list<string> */
    public array $notes = [];

    /**
     * @param array<int|string, string>|null $configured Value => case name from the configuration.
     */
    public function build(Schema $schema, string $class, ?array $configured): EnumDefinition
    {
        $source = $schema->name ?? $class;
        [$values, $titles, $caseDescriptions] = $this->values($schema);
        $backing = $this->backing($schema, $values, $source);

        $fromDescription = $this->namesFromDescription($schema->description());
        $fromExtension = $this->namesFromExtension($schema, $values);
        $names = [];

        if ($configured !== null) {
            $names = $configured;
        } elseif ($fromDescription !== []) {
            $names = $fromDescription;

            foreach ($fromExtension as $value => $name) {
                if (isset($names[$value]) && $names[$value] !== $name) {
                    throw new RuntimeException("{$source}: description names {$value} \"{$names[$value]}\", x-enumNames says \"{$name}\". Configure the names explicitly.");
                }
            }

            foreach (array_diff_key($names, array_flip($values)) as $value => $name) {
                $this->notes[] = "{$source}: {$value} = {$name} is only documented in the description; included.";
            }
        } elseif ($fromExtension !== []) {
            $names = $fromExtension;
        } elseif ($titles !== []) {
            $names = $titles;
        } elseif ($backing === 'string') {
            foreach ($values as $value) {
                $names[$value] = Naming::enumCaseFromValue((string) $value);
            }
        }

        $allValues = array_values(array_unique([...$values, ...array_keys($names)], \SORT_REGULAR));
        $cases = [];
        $usedNames = [];

        foreach ($allValues as $value) {
            $name = $names[$value] ?? null;

            if ($name === null) {
                throw new RuntimeException("{$source}: no name for value {$value}. Configure the names in enumCases.");
            }

            $case = Naming::enumCase($name);

            if (isset($usedNames[strtolower($case)])) {
                throw new RuntimeException("{$source}: case name {$case} is used twice.");
            }

            $usedNames[strtolower($case)] = true;
            $cases[] = new EnumCase($case, $backing === 'int' ? (int) $value : (string) $value, $caseDescriptions[$value] ?? null);
        }

        return new EnumDefinition($class, $backing, $cases, $this->cleanDescription($schema->description()), [$source]);
    }

    /**
     * @return array{list<int|string>, array<int|string, string>, array<int|string, string>}
     */
    private function values(Schema $schema): array
    {
        $values = $schema->enum();

        if ($values !== null) {
            return [$values, [], []];
        }

        $values = [];
        $titles = [];
        $descriptions = [];

        foreach ($schema->variants('oneOf') as $variant) {
            $value = ($variant->enum() ?? [])[0] ?? null;

            if ($value === null) {
                continue;
            }

            $values[] = $value;

            if ($variant->title() !== null) {
                $titles[$value] = $variant->title();
            }

            if ($variant->description() !== null) {
                $descriptions[$value] = $variant->description();
            }
        }

        return [$values, $titles, $descriptions];
    }

    /**
     * @param list<int|string> $values
     *
     * @return 'int'|'string'
     */
    private function backing(Schema $schema, array $values, string $source): string
    {
        $type = $schema->type();
        $allInts = $values !== [] && array_filter($values, is_int(...)) === $values;

        return match (true) {
            $type === 'integer' && $allInts, $type === null && $allInts => 'int',
            $type === 'string' => 'string',
            default => throw new RuntimeException("{$source}: unsupported enum type " . ($type ?? 'none') . '.'),
        };
    }

    /**
     * Parse "0 = Name" lines. Repeated values (aliases like HttpStatusCode's
     * "300 = MultipleChoices" and "300 = Ambiguous") keep their first name.
     *
     * @return array<int|string, string>
     */
    private function namesFromDescription(?string $description): array
    {
        if ($description === null || !preg_match_all('/^\s*(-?\d+)\s*=\s*([A-Za-z_][A-Za-z0-9_]*)\s*$/m', $description, $matches, \PREG_SET_ORDER)) {
            return [];
        }

        $names = [];

        foreach ($matches as $match) {
            $value = (int) $match[1];

            if (isset($names[$value])) {
                $this->notes[] = "alias {$match[2]} for {$value} dropped (keeping {$names[$value]}).";

                continue;
            }

            $names[$value] = $match[2];
        }

        return $names;
    }

    /**
     * @param list<int|string> $values
     *
     * @return array<int|string, string>
     */
    private function namesFromExtension(Schema $schema, array $values): array
    {
        $names = $schema->stringList('x-enumNames');

        if ($names === null || \count($names) !== \count($values)) {
            return [];
        }

        return array_combine($values, $names);
    }

    /**
     * Drop the "0 = Name" lines, which the enum cases already express.
     */
    private function cleanDescription(?string $description): ?string
    {
        if ($description === null) {
            return null;
        }

        $cleaned = trim((string) preg_replace('/^\s*-?\d+\s*=\s*[A-Za-z_][A-Za-z0-9_]*\s*$/m', '', $description));

        return $cleaned === '' ? null : $cleaned;
    }
}
