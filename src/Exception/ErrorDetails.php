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
 *   (`{"title", "detail", "errors", …}`), `{"error": {"errorKey", "message"}}`
 *   or `{"errorResponse": {"errorKey", "message"}}`
 *
 * Field errors of validation problems are appended to the message.
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

        $message = self::firstString($decoded, ['Message', 'message', 'detail', 'title'])
            ?? self::firstString($nested, ['message', 'Message', 'detail', 'title'])
            ?? self::firstString($decoded, ['error']);
        $violations = self::violations($decoded['errors'] ?? null);

        if ($violations !== []) {
            $list = implode('; ', array_map(
                static fn(array $violation): string => $violation['field'] === null ? $violation['message'] : "{$violation['field']}: {$violation['message']}",
                $violations,
            ));
            $message = self::excerpt($message === null ? $list : "{$message} {$list}");
        }

        return new self(
            message: $message,
            errorKey: self::firstString($decoded, ['ErrorKey', 'errorKey'])
                ?? self::firstString($nested, ['errorKey', 'ErrorKey', 'code']),
            field: self::firstString($decoded, ['Field', 'field']) ?? $violations[0]['field'] ?? null,
        );
    }

    /**
     * The field errors of an RFC 7807 validation problem, as a list
     * (`[{"field", "message"}]`, Magic Containers) or as a map
     * (`{"Field": ["message"]}`, ASP.NET).
     *
     * @return list<array{field: string|null, message: string}>
     */
    private static function violations(mixed $errors): array
    {
        if (!\is_array($errors)) {
            return [];
        }

        $violations = [];

        foreach ($errors as $key => $error) {
            if (\is_array($error) && \is_string($error['message'] ?? null)) {
                $violations[] = ['field' => \is_string($error['field'] ?? null) ? $error['field'] : null, 'message' => $error['message']];
            } elseif (\is_string($key) && \is_array($error)) {
                foreach ($error as $message) {
                    if (\is_string($message)) {
                        $violations[] = ['field' => $key, 'message' => $message];
                    }
                }
            }
        }

        return $violations;
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
