# `$bunny->logging->logs->list()`

> CDN Logging API · `GET /v2/pullzones/{pullZoneId}/logs`

Query CDN access logs for a pull zone.

Authenticate with either an `Authorization` bearer JWT or an `AccessKey` header. Filter pushdown happens in ClickHouse where possible; `country` and free-text `search` are applied in-process after fetch.

The time range must lie within the last 3 days. bunny.net allows 30 requests per 10 seconds and pull zone.

## Signature

```php
public function list(
    int $pullZoneId,
    ?DateTimeInterface $from = null,
    ?DateTimeInterface $to = null,
    ?string $status = null,
    ?string $cacheStatus = null,
    ?string $country = null,
    ?string $edgeLocation = null,
    ?string $remoteIp = null,
    ?string $urlContains = null,
    ?string $userAgentContains = null,
    ?string $refererContains = null,
    ?string $search = null,
    ?string $requestId = null,
    ?bool $includeOriginShield = null,
    int $limit = 100,
    int $offset = 0,
    ?string $order = null,
): Page
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$pullZoneId` | `int` | yes |  |
| `$from` | `DateTimeInterface\|null` | no | Inclusive start of the time range (UTC). Defaults to `To - 24h`. Must fall within the 3-day log retention window. The total range (`To - From`) cannot exceed 3 days. |
| `$to` | `DateTimeInterface\|null` | no | Exclusive end of the time range (UTC). Defaults to `now`. |
| `$status` | `string\|null` | no | Comma-separated list of HTTP status filters. Each entry can be an exact code (e.g. `200`, `404`) or a status class (e.g. `2xx`, `5xx`). Multiple entries are combined with OR. |
| `$cacheStatus` | `string\|null` | no | Comma-separated list of cache statuses to match exactly (e.g. `HIT,MISS,EXPIRED`). |
| `$country` | `string\|null` | no | ISO 3166 alpha-2 country code (e.g. `EE`). Multiple values can be comma-separated. |
| `$edgeLocation` | `string\|null` | no | Edge location / server zone (exact match). |
| `$remoteIp` | `string\|null` | no | Client IP address filter (IPv4 or IPv6). The match width adapts to the zone's IP anonymization setting so the filter can never reveal information beyond what the API returns: exact match when anonymization is disabled, /24 (IPv4) or /64 (IPv6) when last-octet anonymization is enabled, and ignored when full anonymization is enabled. |
| `$urlContains` | `string\|null` | no | Case-insensitive substring match against the request URL (host + path). |
| `$userAgentContains` | `string\|null` | no | Case-insensitive substring match against the User-Agent header. |
| `$refererContains` | `string\|null` | no | Case-insensitive substring match against the Referer header. |
| `$search` | `string\|null` | no | Free-text, case-insensitive token search. Tokens are space-separated; a row matches if ANY token appears in ANY of the searched columns: cache status, request ID, edge location, host, path, user agent, referer, and (for zones with extended logging) content range. Remote IP, country code, and the authorization header are not searched. use the dedicated filters for those, or note that the authorization header is encrypted at rest. Limited to 16 tokens of at most 128 characters each. |
| `$requestId` | `string\|null` | no | Exact request ID (UUID) to look up a single log entry. |
| `$includeOriginShield` | `bool\|null` | no | Include origin-shield (edge → shield) requests. Defaults to `false` to match v1. |
| `$limit` | `int` | no | The number of items per page. |
| `$offset` | `int` | no | The number of items to skip. |
| `$order` | `string\|null` | no | Sort order by timestamp: `asc` or `desc` (default). |

## Returns

`Page<LogEntry>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->logging->logs->list(pullZoneId: 123);
```
