<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Exception;

use GoSuccess\Bunny\Http\Request;
use GoSuccess\Bunny\Http\Response;
use RuntimeException;

/**
 * Base class for all errors reported by a bunny.net API as an HTTP error status.
 *
 * The APIs use several error formats; the common details are normalized into
 * {@see $errorKey}, {@see $field} and the exception message, while the raw body
 * stays available in {@see $responseBody}.
 */
class ApiException extends RuntimeException implements BunnyException
{
    /**
     * @param int         $statusCode   HTTP status code of the response.
     * @param string      $responseBody Raw response body.
     * @param string|null $errorKey     Machine-readable error code, e.g. `pullZone.not_found`.
     * @param string|null $field        Name of the offending field, if the API reports one.
     * @param string|null $requestId    Value of the `cdn-requestid` header; quote it
     *                                  when contacting bunny.net support.
     */
    public function __construct(
        string $message,
        public readonly int $statusCode,
        public readonly string $responseBody = '',
        public readonly ?string $errorKey = null,
        public readonly ?string $field = null,
        public readonly ?string $requestId = null,
    ) {
        parent::__construct($message, $statusCode);
    }

    /**
     * Build the most specific exception type for an error response.
     *
     * @param int|null $classifyAs The status that decides the exception type, for an error
     *                             reported in a successful response; the exception keeps the
     *                             actual status in $statusCode.
     */
    public static function fromResponse(Response $response, Request $request, ?int $classifyAs = null): self
    {
        $status = $response->statusCode;
        $body = $response->body;
        $details = ErrorDetails::parse($body);
        $requestId = $response->header('cdn-requestid');
        $requestId = $requestId !== '' ? $requestId : null;

        $target = explode('?', $request->uri, 2)[0];
        $message = "{$request->method->value} {$target} failed with HTTP {$status}";

        if ($response->reasonPhrase !== '') {
            $message .= " {$response->reasonPhrase}";
        }

        if ($details->message !== null) {
            $message .= ": {$details->message}";
        }

        if ($details->errorKey !== null) {
            $message .= " ({$details->errorKey})";
        }

        $arguments = [$message, $status, $body, $details->errorKey, $details->field, $requestId];

        $kind = $classifyAs ?? $status;

        return match (true) {
            $kind === 400 => new BadRequestException(...$arguments),
            $kind === 401 => new AuthenticationException(...$arguments),
            $kind === 403 => new ForbiddenException(...$arguments),
            $kind === 404 => new NotFoundException(...$arguments),
            $kind === 409 => new ConflictException(...$arguments),
            $kind === 422 => new ValidationException(...$arguments),
            $kind === 429 => new RateLimitException(
                ...$arguments,
                retryAfter: RateLimitException::parseRetryAfter($response->header('retry-after')),
            ),
            $kind >= 500 => new ServerException(...$arguments),
            default => new self(...$arguments),
        };
    }

    /**
     * The decoded JSON error body, or null if the body is not JSON.
     */
    public function decodedBody(): mixed
    {
        return $this->responseBody === '' ? null : json_decode($this->responseBody, true);
    }
}
