<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Exception;

use RuntimeException;

/**
 * A signed payload, such as a webhook call, failed verification.
 */
final class InvalidSignatureException extends RuntimeException implements BunnyException {}
