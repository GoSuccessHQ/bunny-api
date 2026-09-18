<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Model;

/**
 * Sentinel for "no value provided", the default of every optional request field.
 *
 * It separates the two meanings that a bare `null` would otherwise conflate:
 *
 * - {@see Undefined::Value} (the default): the field is left out of the request
 *   payload entirely, so the API keeps its current value.
 * - `null`: the field is sent as an explicit JSON `null`, which clears it.
 *
 * Passing it explicitly is only needed to make a field conditional:
 *
 * ```php
 * new PullZoneUpdate(originUrl: $changeOrigin ? 'https://origin.example' : Undefined::Value);
 * ```
 */
enum Undefined
{
    case Value;
}
