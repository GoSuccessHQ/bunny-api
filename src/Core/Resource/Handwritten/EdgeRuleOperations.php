<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Core\Resource\Handwritten;

use GoSuccess\Bunny\Core\Resource\EdgeRuleResource;
use GoSuccess\Bunny\Http\Method;

/**
 * Hand-written methods of {@see EdgeRuleResource}.
 */
trait EdgeRuleOperations
{
    /**
     * Enable or disable an edge rule.
     *
     * The request body's `Id` must be the pull zone id. The specification does
     * not say so; it is documented by other clients (e.g. simplesurance/bunny-go),
     * so this method fills it in instead of asking for it.
     *
     * `POST /pullzone/{pullZoneId}/edgerules/{edgeRuleId}/setEdgeRuleEnabled`
     *
     * @param int    $pullZoneId The ID of the pull zone that contains the edge rule
     * @param string $edgeRuleId The GUID of the edge rule
     * @param bool   $enabled    Whether the edge rule should be enabled
     */
    public function setEnabled(int $pullZoneId, string $edgeRuleId, bool $enabled): void
    {
        $this->connection->json(
            Method::Post,
            "pullzone/{$pullZoneId}/edgerules/{$this->segment($edgeRuleId)}/setEdgeRuleEnabled",
            body: ['Id' => $pullZoneId, 'Value' => $enabled],
        );
    }
}
