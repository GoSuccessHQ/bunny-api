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
     */
    public static function fromResponse(Response $response, Request $request): self
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

        return match (true) {
            $status === 400 => new BadRequestException(...$arguments),
            $status === 401 => new AuthenticationException(...$arguments),
            $status === 403 => new ForbiddenException(...$arguments),
            $status === 404 => new NotFoundException(...$arguments),
            $status === 409 => new ConflictException(...$arguments),
            $status === 422 => new ValidationException(...$arguments),
            $status === 429 => new RateLimitException(
                ...$arguments,
                retryAfter: RateLimitException::parseRetryAfter($response->header('retry-after')),
            ),
            $status >= 500 => new ServerException(...$arguments),
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
