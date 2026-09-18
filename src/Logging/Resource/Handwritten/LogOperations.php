<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Logging\Resource\Handwritten;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\Logging\Model\LegacyLog;
use GoSuccess\Bunny\Logging\Resource\LogResource;

/**
 * Hand-written methods of {@see LogResource}.
 */
trait LogOperations
{
    /**
     * Download the log of one day through the legacy v1 endpoint.
     *
     * bunny.net keeps v1 for existing integrations; list() and all() filter on
     * the server and return structured entries. The file is transferred
     * compressed, stored in a temporary stream and parsed while you iterate.
     *
     * `GET /{date}/{pullZoneId}.log`
     *
     * @param int               $pullZoneId The ID of the pull zone.
     * @param DateTimeInterface $date       The day, taken in UTC; logs are retained for 3 days.
     * @param int|null          $start      The number of lines to skip.
     * @param int|null          $end        The index of the last line to return.
     * @param string|null       $sort       `asc` or `desc` (the default).
     * @param string|null       $status     The status classes to include, e.g. `4,5` (default `2,3,4,5`).
     * @param string|null       $search     Only lines containing this text.
     */
    public function legacy(
        int $pullZoneId,
        DateTimeInterface $date,
        ?int $start = null,
        ?int $end = null,
        ?string $sort = null,
        ?string $status = null,
        ?string $search = null,
    ): LegacyLog {
        $day = DateTimeImmutable::createFromInterface($date)->setTimezone(new DateTimeZone('UTC'))->format('m-d-y');
        $sink = Stream::temporary();

        $this->connection->send(
            Method::Get,
            "{$day}/{$pullZoneId}.log",
            ['start' => $start, 'end' => $end, 'sort' => $sort, 'status' => $status, 'search' => $search],
            headers: ['Accept' => 'text/plain'],
            sink: $sink,
        );

        return new LegacyLog($sink);
    }
}
