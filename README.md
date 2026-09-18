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
| [Core Platform API](https://bunny.net/docs/api-reference/core) | `GoSuccess\Bunny\Core` | `$bunny->core` | ✅ |
| [Origin Errors API](https://bunny.net/docs/cdn/logging/origin-errors) | `GoSuccess\Bunny\OriginErrors` | `$bunny->originErrors` | ✅ |
| [CDN Logging API](https://bunny.net/docs/cdn/logging) | `GoSuccess\Bunny\Logging` | `$bunny->logging` | ✅ |
| [Edge Storage API](https://bunny.net/docs/api-reference/storage) | `GoSuccess\Bunny\Storage` | `$bunny->storage(...)` | ✅ |
| [Stream API](https://bunny.net/docs/api-reference/stream) | `GoSuccess\Bunny\Stream` | `$bunny->stream(...)` | ✅ |
| [Shield API](https://bunny.net/docs/api-reference/shield) | `GoSuccess\Bunny\Shield` | `$bunny->shield` | ✅ |
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

## Origin Errors API

Requests the CDN could not complete because the origin failed: DNS failures,
timeouts (the CDN waits 60 seconds), connection errors and the like.

```php
$log = $bunny->originErrors->get($pullZoneId, new DateTimeImmutable('yesterday'));

foreach ($log->errors as $error) {
    echo $error->timestamp->format('H:i:s'), ' ', $error->statusCode, ' ', $error->errorCode, ' ', $error->requestUrl, PHP_EOL;
}
```

The day is taken in UTC. bunny.net only retains recent days and does not
document how to fetch more errors when `$log->hasMoreData` is set.

## CDN Logging API

Raw request logs of the last 3 days, filtered on the server:

```php
foreach ($bunny->logging->logs->all($pullZoneId, from: new DateTimeImmutable('-24 hours'), status: '4xx,5xx', country: 'DE') as $entry) {
    echo $entry->timestamp->format(DATE_ATOM), ' ', $entry->statusCode, ' ', $entry->url, PHP_EOL;
}
```

`status`, `cacheStatus` and `country` take comma-separated lists; the time
range must lie within the retention window. bunny.net allows 30 requests per 10
seconds and pull zone.

The legacy v1 endpoint returns a whole day as a pipe-delimited file. The client
streams it into a temporary file and parses the lines while you iterate, so
even large logs use little memory:

```php
$log = $bunny->logging->logs->legacy($pullZoneId, new DateTimeImmutable('yesterday'));

foreach ($log as $entry) {
    // LegacyLogEntry
}

// Keep the raw file:
stream_copy_to_stream($log->stream->resource, fopen('access.log', 'wb'));
```

## Edge Storage API

Edge Storage authenticates with the storage zone's password, not the account
API key, and every region has its own host:

```php
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\Storage\StorageRegion;

$storage = $bunny->storage('my-zone', 'zone-password', StorageRegion::NewYork);

// Or straight from the Core API, which returns the zone with its passwords:
$storage = $bunny->storageFor($bunny->core->storageZones->get($storageZoneId));

// Upload from a string or, without loading it into memory, from a stream.
$storage->upload('images/logo.png', file_get_contents('logo.png'));
$storage->upload('videos/intro.mp4', Stream::fromFile('intro.mp4'), contentType: 'video/mp4');

// Download into memory or into a stream.
$html = $storage->get('index.html');
$storage->download('backups/db.sql.gz', Stream::fromFile('db.sql.gz', 'wb'));

foreach ($storage->list('images/') as $object) {
    echo $object->relativePath, ' ', $object->length, ' bytes', PHP_EOL;
}

$storage->describe('images/logo.png');   // metadata, without downloading
$storage->exists('images/logo.png');     // true or false
$storage->createDirectory('archive/2026');
$storage->delete('images/old.png');
$storage->deleteDirectory('archive');    // with everything in it
```

Uploads send the file's SHA-256 checksum, so bunny.net rejects a corrupted
upload; pass `verifyChecksum: false` to skip the extra read. Deleting the root of
a zone requires `deleteDirectory('/', allowRoot: true)`. Transfers are not
limited by the regular request timeout but by `ClientOptions::$transferTimeout`
(unlimited by default; a stalled transfer is aborted after 60 seconds).

`describe()`, `exists()`, `createDirectory()` and the `contentType` of an upload
use parts of the API that the specification does not document; they behave the
way bunny.net's own CLI uses them.

## Stream API

Stream authenticates with the video library's own API key, not the account API
key, and every path contains the library ID. The client takes both once:

```php
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\Stream\Model\VideoUpdate;

$stream = $bunny->stream(12345, 'library-api-key');

// Or straight from the Core API, which returns the library with its API keys:
$stream = $bunny->streamFor($bunny->core->videoLibraries->get(12345));

// Create a video, then upload its file; the file is streamed, not loaded into memory.
$video = $stream->videos->create('Product tour');
$stream->videos->upload($video->guid, Stream::fromFile('tour.mp4'), enabledResolutions: ['720p', '1080p']);

// Or let bunny.net download it; returns the GUID of the new video.
$guid = $stream->videos->fetch('https://example.com/tour.mp4', title: 'Product tour');

foreach ($stream->videos->all(search: 'tour') as $video) {
    echo $video->title, ': ', $video->status?->name, ', ', $video->views, ' views', PHP_EOL;
}

$stream->videos->update($video->guid, new VideoUpdate(title: 'Product tour 2026'));
$stream->videos->addCaption($video->guid, 'en', Stream::fromFile('tour.en.vtt'), label: 'English');
$stream->videos->useGeneratedThumbnail($video->guid, 3);

$statistics = $stream->statistics->get(dateFrom: new DateTimeImmutable('-7 days'), videoGuid: $video->guid);
```

A client with the read-only key (`streamFor($library, readOnly: true)`) can call
every reading method except `videos->storageSize()`, which bunny.net only answers
for the full key.

### Uploads from the browser

Large files are best uploaded resumably with [TUS](https://tus.io), straight
from the browser to bunny.net. Presign the upload on your server, so the API key
never leaves it:

```php
use GoSuccess\Bunny\Stream\Upload\TusUpload;

$video = $stream->videos->create('Product tour');
$upload = TusUpload::presign($stream->libraryId, 'library-api-key', $video->guid, new DateTimeImmutable('+1 day'));

// Hand both to a TUS client such as tus-js-client:
$upload->endpoint;   // https://video.bunnycdn.com/tusupload
$upload->headers;    // AuthorizationSignature, AuthorizationExpire, LibraryId, VideoId
```

TUS clients that expect the raw `Upload-Metadata` header get it from
`TusUpload::metadata('video/mp4', 'Product tour')`.

### Embed token authentication

When a library requires signed embed URLs, sign them with the library's token
authentication key (dashboard → Stream → library → Security):

```php
use GoSuccess\Bunny\Stream\Security\EmbedToken;

$url = EmbedToken::url($libraryId, $videoGuid, 'token-authentication-key', new DateTimeImmutable('+1 hour'), ['autoplay' => true]);
```

### Webhooks

Bunny Stream signs its webhook calls with the library's read-only API key.
`WebhookSignature::parse()` verifies the signature against the exact raw body in
constant time and returns the event:

```php
use GoSuccess\Bunny\Exception\InvalidSignatureException;
use GoSuccess\Bunny\Stream\Webhook\WebhookSignature;
use GoSuccess\Bunny\Stream\Webhook\WebhookStatus;

try {
    $event = WebhookSignature::parse(file_get_contents('php://input'), getallheaders(), 'library-read-only-api-key');
} catch (InvalidSignatureException) {
    http_response_code(401);
    exit;
}

if ($event->status === WebhookStatus::Finished) {
    // The video $event->videoId is encoded and ready to play.
}
```

## Shield API

Shield protects pull zones with a WAF, rate limits, access lists, bot detection,
upload scanning and API Guardian. Everything is organized by Shield zone:

```php
use GoSuccess\Bunny\Shield\Enum\RateLimitAction;
use GoSuccess\Bunny\Shield\Enum\RateLimitBlockTime;
use GoSuccess\Bunny\Shield\Enum\RateLimitCounterKey;
use GoSuccess\Bunny\Shield\Enum\RateLimitTimeframe;
use GoSuccess\Bunny\Shield\Enum\WafRuleOperatorType;
use GoSuccess\Bunny\Shield\Model\RateLimitRuleConfiguration;

$shield = $bunny->shield;
$zone = $shield->zones->getByPullZone($pullZoneId);

echo $zone->planType?->name, ', WAF: ', $zone->wafExecutionMode?->name, PHP_EOL;

// At most 8 login attempts per IP in 10 seconds, then block for a minute.
$shield->rateLimits->create(
    shieldZoneId: $zone->shieldZoneId,
    ruleName: 'Limit login attempts',
    ruleConfiguration: new RateLimitRuleConfiguration(
        actionType: RateLimitAction::RateLimit,
        variableTypes: ['REQUEST_URI' => ''],
        operatorType: WafRuleOperatorType::CONTAINS,
        value: 'wp-login',
        requestCount: 8,
        counterKeyType: RateLimitCounterKey::IP,
        timeframe: RateLimitTimeframe::PerTenSeconds,
        blockTime: RateLimitBlockTime::ForOneMinute,
    ),
);

foreach ($shield->eventLogs->all($zone->shieldZoneId, new DateTimeImmutable('yesterday')) as $log) {
    echo $log->labels?->country, ' ', $log->labels?->ruleId, PHP_EOL;
}

$metrics = $shield->metrics->overview($zone->shieldZoneId);
```

The event logs of the last 72 hours can also be searched, grouped and exported
as CSV:

```php
use GoSuccess\Bunny\Http\Stream;
use GoSuccess\Bunny\Shield\Model\EventLogFilter;

$top = $shield->eventLogs->search(
    $zone->shieldZoneId,
    from: new DateTimeImmutable('-1 hour'),
    to: new DateTimeImmutable(),
    filters: [new EventLogFilter('country', 'in', ['DE', 'AT'])],
    groupBy: ['ip'],
);

$shield->eventLogs->export($zone->shieldZoneId, new DateTimeImmutable('-1 day'), new DateTimeImmutable(), sink: Stream::fromFile('events.csv', 'wb'));
```

Custom block, challenge and rate limit pages are plain HTML:

```php
use GoSuccess\Bunny\Shield\Enum\CustomPageType;

$shield->customPages->upload($zone->shieldZoneId, CustomPageType::Block, file_get_contents('blocked.html'));
```

Shield answers many failures, "not found" among them, with `202 Accepted` and
the error in the body. The client raises them like error statuses (see
[Error handling](#error-handling)), so a missing rule never comes back as an
empty object.

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

The Shield API reports many failures with `202 Accepted` and the error in the
body. These are raised, too: as `NotFoundException` for error keys starting with
`not_found`, otherwise as `ApiException` (e.g. `invalid_plan_type.bot_detection`
for a feature the zone's plan lacks). `$statusCode` keeps the actual status.

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
