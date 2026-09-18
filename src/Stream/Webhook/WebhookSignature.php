<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Stream\Webhook;

use GoSuccess\Bunny\Exception\InvalidSignatureException;
use SensitiveParameter;

/**
 * Verifies that a webhook call was sent by Bunny Stream and not tampered with.
 *
 * Signature version `v1` is an HMAC-SHA256 of the exact raw request body, keyed
 * with the library's read-only API key and sent as lowercase hex in the
 * `X-BunnyStream-Signature` header.
 *
 * ```php
 * $event = WebhookSignature::parse(
 *     body: file_get_contents('php://input'),
 *     headers: getallheaders(),
 *     readOnlyApiKey: $libraryReadOnlyApiKey,
 * );
 * ```
 */
final class WebhookSignature
{
    public const string HEADER_SIGNATURE = 'X-BunnyStream-Signature';
    public const string HEADER_VERSION = 'X-BunnyStream-Signature-Version';
    public const string HEADER_ALGORITHM = 'X-BunnyStream-Signature-Algorithm';

    /**
     * Whether the signature matches the body. The comparison runs in constant
     * time, so it leaks nothing about the expected signature.
     *
     * @param string $body           The exact raw request body; do not re-encode parsed JSON.
     * @param string $signature      The value of the `X-BunnyStream-Signature` header.
     * @param string $readOnlyApiKey The library's read-only API key.
     * @param string $version        The value of the `X-BunnyStream-Signature-Version` header.
     * @param string $algorithm      The value of the `X-BunnyStream-Signature-Algorithm` header.
     */
    public static function verify(
        string $body,
        string $signature,
        #[SensitiveParameter]
        string $readOnlyApiKey,
        string $version = 'v1',
        string $algorithm = 'hmac-sha256',
    ): bool {
        if ($version !== 'v1' || $algorithm !== 'hmac-sha256' || preg_match('/^[0-9a-f]{64}$/', $signature) !== 1) {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $body, $readOnlyApiKey), $signature);
    }

    /**
     * Verify a webhook call and parse its body.
     *
     * @param string                         $body           The exact raw request body.
     * @param array<string, string|string[]> $headers        The request headers; names are matched case-insensitively.
     * @param string                         $readOnlyApiKey The library's read-only API key.
     *
     * @throws InvalidSignatureException If the signature is missing or does not match.
     */
    public static function parse(
        string $body,
        array $headers,
        #[SensitiveParameter]
        string $readOnlyApiKey,
    ): WebhookEvent {
        $normalized = [];

        foreach ($headers as $name => $value) {
            $normalized[strtolower((string) $name)] = \is_array($value) ? (string) reset($value) : $value;
        }

        $valid = self::verify(
            $body,
            $normalized[strtolower(self::HEADER_SIGNATURE)] ?? '',
            $readOnlyApiKey,
            $normalized[strtolower(self::HEADER_VERSION)] ?? '',
            $normalized[strtolower(self::HEADER_ALGORITHM)] ?? '',
        );

        if (!$valid) {
            throw new InvalidSignatureException('The Bunny Stream webhook signature is missing or invalid.');
        }

        return WebhookEvent::fromJson($body);
    }
}
