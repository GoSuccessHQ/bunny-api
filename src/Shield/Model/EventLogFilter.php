<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Shield\Model;

/**
 * A filter of an event log search or export.
 *
 * ```php
 * new EventLogFilter('country', 'in', ['DE', 'AT']);
 * new EventLogFilter('ip', 'cidr', ['203.0.113.0/24']);
 * ```
 */
final readonly class EventLogFilter
{
    /**
     * @param string       $field    The dimension: feature, ruleId, ip, ja4, ua, url, asn, country or action.
     * @param string       $operator eq, in, contains, cidr (ip only, IPv4 or IPv6) or wildcard (`*` matches any run).
     * @param list<string> $values   The values; several values are combined with OR.
     */
    public function __construct(
        public string $field,
        public string $operator,
        public array $values,
    ) {}

    /**
     * @return array{field: string, op: string, value: list<string>}
     */
    public function toArray(): array
    {
        return ['field' => $this->field, 'op' => $this->operator, 'value' => $this->values];
    }
}
