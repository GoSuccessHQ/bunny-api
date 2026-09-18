<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Exception;

/**
 * The access key is valid but not allowed to perform the request (HTTP 403).
 */
final class ForbiddenException extends ApiException {}
