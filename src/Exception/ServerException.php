<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Exception;

/**
 * bunny.net failed to process the request (HTTP 5xx).
 */
final class ServerException extends ApiException {}
