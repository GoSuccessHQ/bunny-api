<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Exception;

/**
 * The access key is missing or invalid (HTTP 401).
 */
final class AuthenticationException extends ApiException {}
