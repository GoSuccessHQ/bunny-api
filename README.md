# bunny.net API – PHP Client

A modern, strongly-typed, **dependency-free** PHP client for the
[bunny.net](https://bunny.net) APIs, built for **PHP 8.4+**.

## Features

- **Fully typed**: `final readonly` models, native enums and generics-annotated
  pagination for every endpoint, checked with PHPStan at the highest level.
- **Zero Composer dependencies**: a self-contained cURL transport (only
  `ext-curl` and `ext-json` are required), pluggable through a small interface.
- **Partial updates done right**: a field you leave out is not sent, a field you
  set to `null` is cleared.
- **Lazy pagination** across all pages with `all()`, or page by page with `list()`.
- **Safe retries**: rate limits (`429`) are retried honoring `Retry-After`;
  server and network errors only for idempotent requests, so an update is never
  applied twice.
- **Typed exceptions** that normalize the error formats of all bunny.net APIs and
  carry the `cdn-requestid` for support cases.
- **Generated from bunny.net's specifications**, with every deviation between
  specification and reality corrected by hand and verified against the live API.

## Supported APIs

| API | Namespace | Access | Status |
| --- | --- | --- | --- |
| [Core Platform API](https://docs.bunny.net/reference/bunnynet-api-overview) | `GoSuccess\Bunny\Core` | `$bunny->core` | ✅ |
| Origin Errors API | `GoSuccess\Bunny\OriginErrors` | | planned |
| CDN Logging API | `GoSuccess\Bunny\Logging` | | planned |
| Edge Storage API | `GoSuccess\Bunny\Storage` | | planned |
| Stream API | `GoSuccess\Bunny\Stream` | | planned |
| Shield API | `GoSuccess\Bunny\Shield` | | planned |
| Edge Scripting API | `GoSuccess\Bunny\EdgeScripting` | | planned |
| Magic Containers API | `GoSuccess\Bunny\MagicContainers` | | planned |

## Requirements

- PHP **8.4** or higher
- `ext-curl` and `ext-json`

## Installation

```bash
composer require gosuccess/bunny-api
```

## Quick start

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-account-api-key');

// Iterate over all pull zones; pages are fetched lazily.
foreach ($bunny->core->pullZones->all() as $pullZone) {
    echo $pullZone->name, ': ', $pullZone->originUrl, PHP_EOL;
}

// Purge a URL from the cache.
$bunny->core->purge->url('https://cdn.example.com/styles.css');
```

`Bunny` creates each API client on first access and lets them share one HTTP
connection pool. Every client can also be used on its own:

```php
use GoSuccess\Bunny\Core\CoreClient;

$core = new CoreClient('your-account-api-key');
$zone = $core->pullZones->get(12345);
```

The account API key is shown in the bunny.net dashboard under
**Account settings → API key**.

## Core Platform API

Pull zones, edge rules, DNS, storage zones, video libraries, statistics, billing,
the audit log and more. See the [API reference](docs/README.md#core-platform-api)
for every method.

### Pull zones and hostnames

```php
use GoSuccess\Bunny\Core\Model\PullZoneCreate;
use GoSuccess\Bunny\Core\Model\PullZoneUpdate;

$zone = $bunny->core->pullZones->create(new PullZoneCreate(
    name: 'my-site',
    originUrl: 'https://origin.example.com',
));

$bunny->core->pullZones->addHostname($zone->id, 'cdn.example.com');
$bunny->core->pullZones->loadFreeCertificate('cdn.example.com');
$bunny->core->pullZones->setForceSsl($zone->id, 'cdn.example.com', forceSSL: true);

// Change a single setting; everything else stays as it is.
$bunny->core->pullZones->update($zone->id, new PullZoneUpdate(cacheControlMaxAgeOverride: 3600));

// Purge the whole zone, or only the files tagged with a cache tag.
$bunny->core->pullZones->purgeCache($zone->id);
$bunny->core->pullZones->purgeCache($zone->id, cacheTag: 'product-42');
```

### Edge rules

```php
use GoSuccess\Bunny\Core\Enum\EdgeRuleActionType;
use GoSuccess\Bunny\Core\Enum\EdgeRuleTriggerType;
use GoSuccess\Bunny\Core\Enum\PatternMatchingType;
use GoSuccess\Bunny\Core\Enum\TriggerMatchingType;
use GoSuccess\Bunny\Core\Model\EdgeRule;
use GoSuccess\Bunny\Core\Model\EdgeRuleTrigger;

$rule = $bunny->core->edgeRules->addOrUpdate($zone->id, new EdgeRule(
    actionType: EdgeRuleActionType::BlockRequest,
    triggers: [
        new EdgeRuleTrigger(
            type: EdgeRuleTriggerType::Url,
            patternMatches: ['*/wp-login.php'],
            patternMatchingType: PatternMatchingType::MatchAny,
        ),
    ],
    triggerMatchingType: TriggerMatchingType::MatchAny,
    description: 'Block the WordPress login',
    enabled: true,
));

// The returned rule carries the GUID bunny.net assigned.
$bunny->core->edgeRules->setEnabled($zone->id, $rule->guid, false);
```

### DNS

```php
use GoSuccess\Bunny\Core\Enum\DnsRecordType;
use GoSuccess\Bunny\Core\Model\DnsRecordCreate;

$dnsZone = $bunny->core->dnsZones->create('example.com');

$bunny->core->dnsRecords->create($dnsZone->id, new DnsRecordCreate(
    type: DnsRecordType::A,
    name: 'www',
    value: '203.0.113.10',
    ttl: 300,
));

foreach ($bunny->core->dnsRecords->all($dnsZone->id) as $record) {
    echo $record->type->name ?? '?', ' ', $record->name, ' ', $record->value, PHP_EOL;
}

// BIND zone files
$zoneFile = $bunny->core->dnsZones->export($dnsZone->id);
$result = $bunny->core->dnsZones->import($dnsZone->id, $zoneFile);
```

### Statistics

```php
$statistics = $bunny->core->statistics->get(
    dateFrom: new DateTimeImmutable('-7 days'),
    pullZone: $zone->id,
    hourly: true,
);

echo $statistics->totalBandwidthUsed, ' bytes, cache hit rate ', $statistics->cacheHitRate, ' %', PHP_EOL;

foreach ($statistics->bandwidthUsedChart as $hour => $bytes) {
    // ...
}
```

Dates are always sent as UTC. Dates bunny.net returns without a time zone are
read as UTC as well.

## Partial updates and clearing fields

Request models serialize **only what you pass**, so an update touches nothing
else. A field you leave out and a field you set to `null` therefore mean two
different things:

```php
use GoSuccess\Bunny\Core\Model\DnsZoneUpdate;

// Only changes the SOA email; everything else keeps its value.
$bunny->core->dnsZones->update($id, new DnsZoneUpdate(soaEmail: 'hostmaster@example.com'));

// Sends an explicit null, which clears the value.
$bunny->core->dnsZones->update($id, new DnsZoneUpdate(soaEmail: null));
```

To make a field conditional, pass the `Undefined` sentinel, which is the default
of every optional field:

```php
use GoSuccess\Bunny\Model\Undefined;

new DnsZoneUpdate(soaEmail: $changeEmail ? 'hostmaster@example.com' : Undefined::Value);
```

## Pagination

List endpoints come in pairs: `list()` returns one `Page`, `all()` returns a lazy
`Paginator` over every item of every page.

```php
$page = $bunny->core->dnsZones->list(page: 1, perPage: 100);

echo $page->totalItems, ' zones', PHP_EOL;
echo $page->hasMore ? 'more pages follow' : 'last page', PHP_EOL;

foreach ($page->items as $dnsZone) {
    // ...
}

// Every zone, fetching pages only while the loop runs:
foreach ($bunny->core->dnsZones->all(search: 'example') as $dnsZone) {
    // ...
}
```

## Error handling

Every exception the library throws implements
`GoSuccess\Bunny\Exception\BunnyException`. Error responses become typed
exceptions:

| Status | Exception |
| --- | --- |
| 400 | `BadRequestException` |
| 401 | `AuthenticationException` |
| 403 | `ForbiddenException` |
| 404 | `NotFoundException` |
| 409 | `ConflictException` |
| 422 | `ValidationException` |
| 429 | `RateLimitException` (with `$retryAfter`) |
| 5xx | `ServerException` |
| other | `ApiException` |

All of them extend `ApiException`, which carries `$statusCode`, `$errorKey` (e.g.
`pullZone.not_found`), `$field`, `$requestId` (quote it when contacting bunny.net
support) and the raw `$responseBody`. Network failures throw a
`TransportException`, unexpected response bodies a `SerializationException`.

```php
use GoSuccess\Bunny\Exception\NotFoundException;

try {
    $bunny->core->pullZones->get(12345);
} catch (NotFoundException $e) {
    echo $e->getMessage(), ' (', $e->errorKey, ')', PHP_EOL;
}
```

## Configuration

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\ClientOptions;

$bunny = new Bunny('your-account-api-key', new ClientOptions(
    timeout: 30.0,          // seconds per regular request
    connectTimeout: 10.0,
    transferTimeout: 0.0,   // seconds per streamed up- or download; 0 = no limit
    maxRetries: 3,
    retryBaseDelay: 1.0,    // exponential backoff: 1 s, 2 s, 4 s, ...
    maxRetryDelay: 60.0,    // also caps a server-provided Retry-After
));
```

A `429` is always retried. A server error or a lost connection is only retried
for `GET`, `PUT` and `DELETE`, because bunny.net applies most changes via `POST`
and repeating such a request could apply it twice.

### Rate limiting

bunny.net answers excess requests with `429`, which the client retries. To stay
below a limit proactively, pass a rate limiter, e.g. the built-in sliding
window, or your own implementation of `RateLimiter` (for example backed by
Redis to share it across processes):

```php
use GoSuccess\Bunny\RateLimit\SlidingWindowRateLimiter;

$bunny = new Bunny('your-account-api-key', rateLimiter: new SlidingWindowRateLimiter(maxRequests: 100, windowSeconds: 60.0));
```

### Custom transport

Implement `GoSuccess\Bunny\Http\HttpClient` to route requests through your own
HTTP stack, e.g. Guzzle, without the library depending on it:

```php
use GoSuccess\Bunny\Http\HttpClient;
use GoSuccess\Bunny\Http\Request;
use GoSuccess\Bunny\Http\Response;

final class GuzzleTransport implements HttpClient
{
    public function send(Request $request): Response { /* ... */ }
}

$bunny = new Bunny('your-account-api-key', httpClient: new GuzzleTransport());
```

The built-in cURL transport keeps connections alive and, on PHP 8.5, shares DNS,
connection and TLS session caches across requests of a PHP worker.

## Documentation and examples

- **[docs/](docs/README.md)**: a reference page for every method, with the
  endpoint, signature, parameters and an example.
- **[examples/](examples/)**: runnable scripts. They only read data, so they
  are safe to run against a production account.

## Development

The enums, models, resources and clients under `src/<Api>/` (except the
`Handwritten/` directories) are generated from the committed snapshots of
bunny.net's specifications in [resources/specs/](resources/specs/). Naming
decisions and every correction of the specifications live in
[tools/config/](tools/config/), each correction with the evidence it is based
on.

```bash
composer generate   # regenerate code and docs from the snapshots
composer check      # php-cs-fixer, PHPStan (level max) and the unit tests
composer specs      # refresh the snapshots from bunny.net, then run "composer generate"
```

CI regenerates everything and fails if the committed files are out of date.

The read-only integration tests check the client against a real account. They
only send `GET` requests without side effects:

```bash
BUNNY_API_KEY=your-api-key composer test:integration
```

## License

[MIT](LICENSE)
