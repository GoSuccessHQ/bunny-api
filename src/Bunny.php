<?php

declare(strict_types=1);

namespace GoSuccess\Bunny;

use GoSuccess\Bunny\Core\CoreClient;
use GoSuccess\Bunny\Http\CurlHttpClient;
use GoSuccess\Bunny\Http\HttpClient;
use GoSuccess\Bunny\RateLimit\NullRateLimiter;
use GoSuccess\Bunny\RateLimit\RateLimiter;
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
     * Hide the API key from var_dump() and print_r().
     *
     * @return array<string, string>
     */
    public function __debugInfo(): array
    {
        return ['apiKey' => '********'];
    }
}
