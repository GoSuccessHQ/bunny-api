<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Shield\Model;

use DateTimeImmutable;
use GoSuccess\Bunny\Model\Cast;
use GoSuccess\Bunny\Model\ResponseModel;

/**
 * The Shield promotions of the account.
 *
 * The specification documents no response body; the fields are those the API
 * returns. The structure of a promotion is unknown, so promotions are kept as
 * the API sends them.
 */
final readonly class PromotionState implements ResponseModel
{
    /**
     * @param list<mixed>            $currentPromos               The promotions currently running.
     * @param list<mixed>            $eligiblePromos              The promotions the account can enroll in.
     * @param list<mixed>            $enrolledPromos              The promotions the account is enrolled in.
     * @param DateTimeImmutable|null $firstShieldZoneCreationDate When the account created its first Shield zone.
     */
    public function __construct(
        public array $currentPromos = [],
        public array $eligiblePromos = [],
        public array $enrolledPromos = [],
        public ?DateTimeImmutable $firstShieldZoneCreationDate = null,
    ) {}

    public static function fromArray(array $data): static
    {
        $asIs = static fn(mixed $value): mixed => $value;

        return new self(
            currentPromos: Cast::listOf($data['currentPromos'] ?? null, $asIs),
            eligiblePromos: Cast::listOf($data['eligiblePromos'] ?? null, $asIs),
            enrolledPromos: Cast::listOf($data['enrolledPromos'] ?? null, $asIs),
            firstShieldZoneCreationDate: Cast::dateTime($data['firstShieldZoneCreationDate'] ?? null),
        );
    }
}
