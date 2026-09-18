<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Shield\Resource\Handwritten;

use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Shield\Model\PromotionState;
use GoSuccess\Bunny\Shield\Resource\PromotionResource;

/**
 * Hand-written methods of {@see PromotionResource}.
 */
trait PromotionOperations
{
    /**
     * Get the Shield promotions of the account.
     *
     * `GET /shield/promo/state`
     */
    public function state(): PromotionState
    {
        return self::toModel(PromotionState::class, $this->connection->json(Method::Get, 'shield/promo/state'));
    }
}
