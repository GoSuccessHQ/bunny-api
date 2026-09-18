<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Shield\Resource\Handwritten;

use GoSuccess\Bunny\Exception\SerializationException;
use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\Shield\Enum\CustomPageType;
use GoSuccess\Bunny\Shield\Resource\CustomPageResource;

/**
 * Hand-written methods of {@see CustomPageResource}.
 *
 * Pages travel as raw HTML in both directions, the way bunny.net's Terraform
 * provider sends and reads them. Custom pages are not available on the Basic
 * plan; the API then answers 400 `feature_not_available_on_plan`.
 */
trait CustomPageOperations
{
    /**
     * Get the HTML of a custom page.
     *
     * `GET /shield/shield-zone/{shieldZoneId}/custom-page/{pageType}`
     *
     * @param int            $shieldZoneId The ID of the Shield zone.
     * @param CustomPageType $type         The page.
     */
    public function get(int $shieldZoneId, CustomPageType $type): string
    {
        $response = $this->connection->send(
            Method::Get,
            "shield/shield-zone/{$shieldZoneId}/custom-page/{$type->value}",
            headers: ['Accept' => 'text/plain'],
        );

        if (!str_contains(strtolower($response->header('content-type')), 'json')) {
            return $response->body;
        }

        // Should the API answer with JSON after all, the page is a JSON string.
        $html = json_decode($response->body, true);

        if (!\is_string($html)) {
            throw new SerializationException('Expected the custom page as a string.');
        }

        return $html;
    }

    /**
     * Upload the HTML of a custom page, replacing the current one.
     *
     * `PUT /shield/shield-zone/{shieldZoneId}/custom-page/{pageType}`
     *
     * @param int            $shieldZoneId The ID of the Shield zone.
     * @param CustomPageType $type         The page.
     * @param string|Stream  $html         The HTML of the page.
     */
    public function upload(int $shieldZoneId, CustomPageType $type, string|Stream $html): void
    {
        $this->connection->send(
            Method::Put,
            "shield/shield-zone/{$shieldZoneId}/custom-page/{$type->value}",
            body: $html,
            // The raw page with a JSON content type, exactly as the Terraform provider sends it.
            headers: ['Content-Type' => 'application/json'],
        );
    }

    /**
     * Delete a custom page; bunny.net's own page is shown again.
     *
     * `DELETE /shield/shield-zone/{shieldZoneId}/custom-page/{pageType}`
     *
     * @param int            $shieldZoneId The ID of the Shield zone.
     * @param CustomPageType $type         The page.
     */
    public function delete(int $shieldZoneId, CustomPageType $type): void
    {
        $this->connection->send(Method::Delete, "shield/shield-zone/{$shieldZoneId}/custom-page/{$type->value}");
    }
}
