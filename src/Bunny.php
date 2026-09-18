<?php

declare(strict_types=1);

namespace GoSuccess\Bunny;

use GoSuccess\Bunny\Core\CoreClient;
use GoSuccess\Bunny\Core\Model\StorageZone;
use GoSuccess\Bunny\Core\Model\VideoLibrary;
use GoSuccess\Bunny\Http\CurlHttpClient;
use GoSuccess\Bunny\Http\HttpClient;
use GoSuccess\Bunny\Logging\LoggingClient;
use GoSuccess\Bunny\OriginErrors\OriginErrorsClient;
use GoSuccess\Bunny\RateLimit\NullRateLimiter;
use GoSuccess\Bunny\RateLimit\RateLimiter;
use GoSuccess\Bunny\Storage\StorageClient;
use GoSuccess\Bunny\Storage\StorageRegion;
use GoSuccess\Bunny\Stream\StreamClient;
use InvalidArgumentException;
use SensitiveParameter;

/**
 * Entry point to all bunny.net APIs that authenticate with the account API key.
 *
 * ```php
 * $bunny = new Bunny('account-api-key');
 *
 * foreach ($bunny->core->pullZones->all() as $pullZone) {
 *     echo $pullZone->name, PHP_EOL;
 * }
 * ```
 *
 * Every API client is created on first access and shares one HTTP transport,
 * so connections are kept alive across APIs. Each client can also be used on
 * its own, e.g. `new CoreClient('account-api-key')`.
 */
final class Bunny
{
    /**
     * The Core Platform API: pull zones, edge rules, DNS, storage zones, video
     * libraries, statistics, billing and more.
     */
    public private(set) CoreClient $core {
        get => $this->core ??= new CoreClient($this->apiKey, $this->options, $this->httpClient, $this->rateLimiter);
    }

    /**
     * The Origin Errors API: requests the CDN could not complete because the
     * origin failed.
     */
    public private(set) OriginErrorsClient $originErrors {
        get => $this->originErrors ??= new OriginErrorsClient($this->apiKey, $this->options, $this->httpClient, $this->rateLimiter);
    }

    /**
     * The CDN Logging API: raw request logs of the last 3 days.
     */
    public private(set) LoggingClient $logging {
        get => $this->logging ??= new LoggingClient($this->apiKey, $this->options, $this->httpClient, $this->rateLimiter);
    }

    private readonly HttpClient $httpClient;

    /**
     * @param string          $apiKey      The account API key (bunny.net dashboard → Account settings → API key).
     * @param ClientOptions   $options     Timeouts, retries and user agent, shared by all API clients.
     * @param HttpClient|null $httpClient  Custom transport; defaults to the built-in cURL transport.
     * @param RateLimiter     $rateLimiter Client-side throttling shared by all API clients; disabled by default.
     */
    public function __construct(
        #[SensitiveParameter]
        private readonly string $apiKey,
        private readonly ClientOptions $options = new ClientOptions(),
        ?HttpClient $httpClient = null,
        private readonly RateLimiter $rateLimiter = new NullRateLimiter(),
    ) {
        $this->httpClient = $httpClient ?? new CurlHttpClient($options->timeout, $options->connectTimeout);
    }

    /**
     * A client for the Edge Storage API of one storage zone.
     *
     * Edge Storage authenticates with the zone's password instead of the
     * account API key; the read-only password works for listing and downloading.
     */
    public function storage(
        string $zone,
        #[SensitiveParameter]
        string $password,
        StorageRegion $region = StorageRegion::Falkenstein,
    ): StorageClient {
        return new StorageClient($zone, $password, $region, $this->options, $this->httpClient, $this->rateLimiter);
    }

    /**
     * A client for a storage zone as the Core API returns it, e.g. from
     * `$bunny->core->storageZones->get($id)`, which includes the zone's passwords.
     *
     * @param bool $readOnly Use the read-only password, which only allows listing and downloading.
     */
    public function storageFor(StorageZone $zone, bool $readOnly = false): StorageClient
    {
        $password = $readOnly ? $zone->readOnlyPassword : $zone->password;

        if ($zone->name === null || $password === null || $password === '') {
            throw new InvalidArgumentException('The storage zone has no name or password; fetch it with $bunny->core->storageZones->get().');
        }

        $region = StorageRegion::tryFrom(strtolower($zone->region ?? '')) ?? StorageRegion::Falkenstein;
        $host = $zone->storageHostname;

        return new StorageClient(
            $zone->name,
            $password,
            $region,
            $this->options,
            $this->httpClient,
            $this->rateLimiter,
            $host !== null && $host !== '' ? "https://{$host}" : null,
        );
    }

    /**
     * A client for the Stream API of one video library.
     *
     * Stream authenticates with the library's API key instead of the account
     * API key; the read-only API key works for reading.
     */
    public function stream(
        int $libraryId,
        #[SensitiveParameter]
        string $apiKey,
    ): StreamClient {
        return new StreamClient($libraryId, $apiKey, $this->options, $this->httpClient, $this->rateLimiter);
    }

    /**
     * A client for a video library as the Core API returns it, e.g. from
     * `$bunny->core->videoLibraries->get($id)`, which includes its API keys.
     *
     * @param bool $readOnly Use the read-only API key.
     */
    public function streamFor(VideoLibrary $library, bool $readOnly = false): StreamClient
    {
        $apiKey = $readOnly ? $library->readOnlyApiKey : $library->apiKey;

        if ($apiKey === null || $apiKey === '') {
            throw new InvalidArgumentException('The video library has no API key; fetch it with $bunny->core->videoLibraries->get().');
        }

        return $this->stream($library->id, $apiKey);
    }

    /**
     * Hide the API key from var_dump() and print_r().
     *
     * @return array<string, string>
     */
    public function __debugInfo(): array
    {
        return ['apiKey' => '********'];
    }
}
