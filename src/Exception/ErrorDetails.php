<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Exception;

/**
 * Normalizes the error formats used across the bunny.net APIs.
 *
 * - Core and Edge Scripting: `{"ErrorKey", "Field", "Message"}`
 * - Edge Storage: `{"HttpCode", "Message"}`
 * - Stream: `{"success", "message", "statusCode"}`
 * - Logging: `{"error": {"code", "message", "details"}}`
 * - Shield and Magic Containers: RFC 7807 problem details
 *   (`{"title", "detail", …}`), `{"error": {"errorKey", "message"}}` or
 *   `{"errorResponse": {"errorKey", "message"}}`
 *
 * @internal
 */
final readonly class ErrorDetails
{
    private const int MAX_MESSAGE_BYTES = 500;

    private function __construct(
        public ?string $message,
        public ?string $errorKey,
        public ?string $field,
    ) {}

    public static function parse(string $body): self
    {
        if (trim($body) === '') {
            return new self(null, null, null);
        }

        $decoded = json_decode($body, true);

        if (!\is_array($decoded)) {
            // Plain text or an HTML error page.
            return new self(self::excerpt($body), null, null);
        }

        $nested = match (true) {
            \is_array($decoded['error'] ?? null) => $decoded['error'],
            \is_array($decoded['errorResponse'] ?? null) => $decoded['errorResponse'],
            default => [],
        };

        return new self(
            message: self::firstString($decoded, ['Message', 'message', 'detail', 'title'])
                ?? self::firstString($nested, ['message', 'Message', 'detail', 'title'])
                ?? self::firstString($decoded, ['error']),
            errorKey: self::firstString($decoded, ['ErrorKey', 'errorKey'])
                ?? self::firstString($nested, ['errorKey', 'ErrorKey', 'code']),
            field: self::firstString($decoded, ['Field', 'field']),
        );
    }

    /**
     * @param array<array-key, mixed> $data
     * @param list<string>            $keys
     */
    private static function firstString(array $data, array $keys): ?string
    {
        foreach ($keys as $key) {
            $value = $data[$key] ?? null;

            if (\is_string($value) && trim($value) !== '') {
                return self::excerpt($value);
            }
        }

        return null;
    }

    /**
     * Shorten long bodies (e.g. HTML pages) without breaking a UTF-8 sequence.
     */
    private static function excerpt(string $text): string
    {
        $text = trim($text);

        if (\strlen($text) <= self::MAX_MESSAGE_BYTES) {
            return $text;
        }

        $cut = substr($text, 0, self::MAX_MESSAGE_BYTES);

        // Drop a trailing, incomplete multi-byte sequence.
        return (preg_replace('/[\xC0-\xFF][\x80-\xBF]*$/', '', $cut) ?? $cut) . '…';
    }
}
