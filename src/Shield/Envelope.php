<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Shield;

use GoSuccess\Bunny\Http\Response;

/**
 * Spots failures that the Shield API reports with a misleading status.
 *
 * Shield answers many failures with `202 Accepted` and the error in the body,
 * e.g. `{"data": null, "error": {"success": false, "message": "…", "errorKey":
 * "not_found_or_unauthorised_access.shieldzone"}}` for an unknown zone or
 * `invalid_plan_type.bot_detection` for a feature the zone's plan lacks
 * (verified live). bunny.net's Terraform provider treats these responses as
 * failures, too, and maps the `not_found` keys to "not found".
 *
 * @internal
 */
final class Envelope
{
    /**
     * The status classifying the failure the body reports, or null if it
     * reports none.
     *
     * A failure is `success: false` together with a message or error key, at
     * the top level or in `error` or `errorResponse`; the empty defaults of a
     * successful response carry neither. Error keys starting with `not_found`
     * classify as 404, a 401 with an error key as 400, since a failed
     * authentication has none (verified live), others as the status in the
     * body if that is an error status, else as the actual status.
     */
    public static function errorStatus(Response $response): ?int
    {
        // Spares decoding large successful bodies twice.
        if (preg_match('/"success"\s*:\s*false/', $response->body) !== 1) {
            return null;
        }

        $data = json_decode($response->body, true);

        if (!\is_array($data)) {
            return null;
        }

        foreach ([$data, $data['error'] ?? null, $data['errorResponse'] ?? null] as $candidate) {
            if (!\is_array($candidate) || ($candidate['success'] ?? null) !== false) {
                continue;
            }

            $errorKey = self::filled($candidate['errorKey'] ?? null);

            if ($errorKey === null && self::filled($candidate['message'] ?? null) === null) {
                continue;
            }

            if ($errorKey !== null && str_starts_with($errorKey, 'not_found')) {
                return 404;
            }

            $status = $candidate['statusCode'] ?? null;
            $status = \is_int($status) && $status >= 400 ? $status : $response->statusCode;

            // A failed authentication comes as bare problem details; a 401 with an
            // error key rejects the request itself, e.g. event logs of a day
            // outside the last three (invalid_datetime_window.event_logs).
            return $status === 401 && $errorKey !== null ? 400 : $status;
        }

        return null;
    }

    private static function filled(mixed $value): ?string
    {
        return \is_string($value) && trim($value) !== '' ? $value : null;
    }
}
