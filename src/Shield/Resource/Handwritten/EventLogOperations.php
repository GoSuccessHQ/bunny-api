<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Shield\Resource\Handwritten;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use GoSuccess\Bunny\Exception\BadRequestException;
use GoSuccess\Bunny\Http\Connection;
use GoSuccess\Bunny\Http\Method;
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\Model\Cast;
use GoSuccess\Bunny\Pagination\Page;
use GoSuccess\Bunny\Pagination\Paginator;
use GoSuccess\Bunny\Shield\Model\EventLog;
use GoSuccess\Bunny\Shield\Model\EventLogFilter;
use GoSuccess\Bunny\Shield\Model\EventLogSearchResult;
use GoSuccess\Bunny\Shield\Resource\EventLogResource;

/**
 * Hand-written methods of {@see EventLogResource}.
 */
trait EventLogOperations
{
    /**
     * Get a page of the event logs of one day.
     *
     * Only the day counts, taken in UTC; its time is ignored. bunny.net keeps
     * the event logs of today and the two days before (verified live) and
     * rejects older days with invalid_datetime_window.event_logs.
     *
     * The first page is requested without a continuation token (verified live);
     * each further page with the token of the previous one.
     *
     * `GET /shield/event-logs/{shieldZoneId}/{date}/{continuationToken}`
     *
     * @param int               $shieldZoneId      The ID of the Shield zone.
     * @param DateTimeInterface $date              The day, taken in UTC.
     * @param string|null       $continuationToken The position returned by the previous page; null for the first page.
     *
     * @return Page<EventLog>
     *
     * @throws BadRequestException If the day lies outside the kept event logs.
     */
    public function list(int $shieldZoneId, DateTimeInterface $date, ?string $continuationToken = null): Page
    {
        $day = DateTimeImmutable::createFromInterface($date)->setTimezone(new DateTimeZone('UTC'))->format('m-d-Y');
        $path = "shield/event-logs/{$shieldZoneId}/{$day}";

        if ($continuationToken !== null) {
            $path .= "/{$this->segment($continuationToken)}";
        }

        $data = self::expectObject($this->connection->json(Method::Get, $path));
        $next = Cast::string($data['continuationToken'] ?? null);
        // The API sends "" for "no token".
        $hasMore = (Cast::bool($data['hasMoreData'] ?? null) ?? false) && $next !== null && $next !== '' && $next !== $continuationToken;

        return new Page(
            items: Cast::modelList(EventLog::class, $data['logs'] ?? null),
            next: $hasMore ? $next : null,
        );
    }

    /**
     * Iterate lazily over all event logs of one day, across all pages.
     *
     * Only the day counts, taken in UTC; bunny.net keeps the event logs of
     * today and the two days before, see list().
     *
     * `GET /shield/event-logs/{shieldZoneId}/{date}/{continuationToken}`
     *
     * @param int               $shieldZoneId The ID of the Shield zone.
     * @param DateTimeInterface $date         The day, taken in UTC.
     *
     * @return Paginator<EventLog>
     *
     * @throws BadRequestException If the day lies outside the kept event logs.
     */
    public function all(int $shieldZoneId, DateTimeInterface $date): Paginator
    {
        return new Paginator(fn(int|string|null $position): Page => $this->list(
            shieldZoneId: $shieldZoneId,
            date: $date,
            continuationToken: \is_string($position) ? $position : null,
        ));
    }

    /**
     * Search, filter and group the event logs of a time window.
     *
     * `POST /shield/event-logs/{shieldZoneId}/search`
     *
     * @param int                  $shieldZoneId The ID of the Shield zone.
     * @param DateTimeInterface    $from         The start of the window.
     * @param DateTimeInterface    $to           The end of the window; after $from and within the last 72 hours.
     * @param string|null          $query        Free text searched in IP, rule ID, URL, user agent and rule name.
     * @param list<EventLogFilter> $filters      Filters, combined with AND.
     * @param list<string>         $groupBy      Dimensions to group by, in order, e.g. `['ip', 'ja4']`: feature,
     *                                           ruleId, ip, ja4, ua, url, asn, country or action. Empty for rows.
     * @param int|null             $buckets      With grouping: the number of time buckets of each group's
     *                                           sparkline, up to 500; 0 or null for none.
     * @param int                  $page         The page to return, starting at 0.
     * @param int|null             $pageSize     Rows or groups per page, 1 to 500; 50 by default.
     */
    public function search(
        int $shieldZoneId,
        DateTimeInterface $from,
        DateTimeInterface $to,
        ?string $query = null,
        array $filters = [],
        array $groupBy = [],
        ?int $buckets = null,
        int $page = 0,
        ?int $pageSize = null,
    ): EventLogSearchResult {
        $body = self::window($from, $to, $query, $filters);

        if ($groupBy !== []) {
            $body['groupBy'] = $groupBy;
        }

        if ($buckets !== null) {
            $body['buckets'] = $buckets;
        }

        $body['page'] = $page;

        if ($pageSize !== null) {
            $body['pageSize'] = $pageSize;
        }

        return self::toModel(
            EventLogSearchResult::class,
            $this->connection->json(Method::Post, "shield/event-logs/{$shieldZoneId}/search", body: $body),
        );
    }

    /**
     * Export the filtered event logs of a time window as CSV.
     *
     * `POST /shield/event-logs/{shieldZoneId}/export`
     *
     * @param int                  $shieldZoneId The ID of the Shield zone.
     * @param DateTimeInterface    $from         The start of the window.
     * @param DateTimeInterface    $to           The end of the window; after $from and within the last 72 hours.
     * @param string|null          $query        Free text searched in IP, rule ID, URL, user agent and rule name.
     * @param list<EventLogFilter> $filters      Filters, combined with AND.
     * @param Stream|null          $sink         Write the CSV into this stream instead of returning it,
     *                                           e.g. `Stream::fromFile('events.csv', 'wb')`.
     *
     * @return string The CSV, or an empty string if it was written into $sink.
     */
    public function export(
        int $shieldZoneId,
        DateTimeInterface $from,
        DateTimeInterface $to,
        ?string $query = null,
        array $filters = [],
        ?Stream $sink = null,
    ): string {
        $response = $this->connection->send(
            Method::Post,
            "shield/event-logs/{$shieldZoneId}/export",
            body: Connection::encodeJson(self::window($from, $to, $query, $filters)),
            headers: ['Accept' => 'text/csv', 'Content-Type' => 'application/json'],
            sink: $sink,
        );

        return $sink === null ? $response->body : '';
    }

    /**
     * The window and filters shared by search and export.
     *
     * @param list<EventLogFilter> $filters
     *
     * @return array<string, mixed>
     */
    private static function window(DateTimeInterface $from, DateTimeInterface $to, ?string $query, array $filters): array
    {
        $body = ['from' => self::milliseconds($from), 'to' => self::milliseconds($to)];

        if ($query !== null) {
            $body['query'] = $query;
        }

        if ($filters !== []) {
            $body['filters'] = array_map(static fn(EventLogFilter $filter): array => $filter->toArray(), $filters);
        }

        return $body;
    }

    private static function milliseconds(DateTimeInterface $date): int
    {
        return $date->getTimestamp() * 1000 + (int) $date->format('v');
    }
}
