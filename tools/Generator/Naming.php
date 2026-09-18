<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Tools\Generator;

use RuntimeException;

/**
 * Deterministic naming rules for generated code.
 */
final class Naming
{
    /**
     * Mixed-case acronyms that would otherwise be split wrongly at the start of a
     * name, e.g. "IPv6Enabled" would become "iPv6Enabled".
     */
    private const array LEADING_TOKENS = ['IPv4' => 'ipv4', 'IPv6' => 'ipv6', 'OAuth' => 'oauth'];

    /**
     * camelCase name for a property or parameter, derived from a JSON key.
     *
     * "OriginUrl" → "originUrl", "CNAMEDomain" → "cnameDomain", "ID" → "id",
     * "created_at" → "createdAt", "EnableGeoZoneUS" → "enableGeoZoneUS".
     */
    public static function camel(string $key): string
    {
        $words = preg_split('/[^A-Za-z0-9]+/', $key, -1, \PREG_SPLIT_NO_EMPTY) ?: [];

        if ($words === []) {
            throw new RuntimeException("Cannot derive a name from \"{$key}\".");
        }

        $first = self::lowerLeading(array_shift($words));
        $rest = array_map(static fn(string $word): string => ucfirst($word), $words);
        $name = $first . implode('', $rest);

        return preg_match('/^\d/', $name) ? "value{$name}" : $name;
    }

    /**
     * PascalCase class name: "pull_zone" → "PullZone"; inner capitals are kept.
     */
    public static function pascal(string $value): string
    {
        $words = preg_split('/[^A-Za-z0-9]+/', $value, -1, \PREG_SPLIT_NO_EMPTY) ?: [];
        $name = implode('', array_map(static fn(string $word): string => ucfirst($word), $words));

        if ($name === '') {
            throw new RuntimeException("Cannot derive a class name from \"{$value}\".");
        }

        return preg_match('/^\d/', $name) ? "Value{$name}" : $name;
    }

    /**
     * Enum case name. Names from the specification are kept as they are when
     * they are valid identifiers ("AAAA", "ForceSSL", "CDN_Standard_Tier_EU_Traffic");
     * anything else is converted to PascalCase.
     */
    public static function enumCase(string $name): string
    {
        $case = preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name) === 1 ? $name : self::pascal($name);

        // "class" is the only name a class constant cannot have.
        return strtolower($case) === 'class' ? "{$case}Value" : $case;
    }

    /**
     * Case name derived from a string enum value: "notStarted" → "NotStarted",
     * "iPv4" → "IPv4", "ifNotPresent" → "IfNotPresent".
     */
    public static function enumCaseFromValue(string $value): string
    {
        return self::enumCase(self::pascal($value));
    }

    private static function lowerLeading(string $word): string
    {
        foreach (self::LEADING_TOKENS as $token => $lower) {
            if (str_starts_with($word, $token)) {
                return $lower . substr($word, \strlen($token));
            }
        }

        if (!preg_match('/^[A-Z]+/', $word, $match)) {
            return $word;
        }

        $run = $match[0];
        $length = \strlen($run);

        if ($length === \strlen($word) || $length === 1) {
            // "ID" → "id", "Origin" → "origin"
            return strtolower($run) . substr($word, $length);
        }

        $next = $word[$length];

        if (ctype_lower($next)) {
            // "CNAMEDomain" → "cname" + "Domain"
            return strtolower(substr($run, 0, -1)) . substr($word, $length - 1);
        }

        // "S3Type", "URL2" → lower the whole run
        return strtolower($run) . substr($word, $length);
    }
}
