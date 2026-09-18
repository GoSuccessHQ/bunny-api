# `statistics->get()`

> Core Platform API · `GET /statistics`

Get Statistics

## Signature

```php
public function get(
    ?DateTimeInterface $dateFrom = null,
    ?DateTimeInterface $dateTo = null,
    ?int $pullZone = null,
    ?int $serverZoneId = null,
    ?bool $loadErrors = null,
    ?bool $hourly = null,
    ?bool $exactRange = null,
    ?bool $loadOriginResponseTimes = null,
    ?bool $loadOriginTraffic = null,
    ?bool $loadRequestsServed = null,
    ?bool $loadBandwidthUsed = null,
    ?bool $loadOriginShieldBandwidth = null,
    ?bool $loadGeographicTrafficDistribution = null,
    ?bool $loadUserBalanceHistory = null,
): Statistics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$dateFrom` | `DateTimeInterface\|null` | no | (Optional) The start date of the statistics. If no value is passed, the last 30 days will be returned. |
| `$dateTo` | `DateTimeInterface\|null` | no | (Optional) The end date of the statistics. If no value is passed, the last 30 days will be returned. |
| `$pullZone` | `int\|null` | no | (Optional) If set, the statistics will be only returned for the given Pull Zone |
| `$serverZoneId` | `int\|null` | no | (Optional) If set, the statistics will be only returned for the given region ID |
| `$loadErrors` | `bool\|null` | no | (Optional) If set, the respose will contain the non-2xx response |
| `$hourly` | `bool\|null` | no | (Optional) If true, the statistics data will be returned in hourly groupping. |
| `$exactRange` | `bool\|null` | no | (Optional) If true and hourly=true, the exact hour components of dateFrom and dateTo will be preserved instead of rounding to full-day boundaries. |
| `$loadOriginResponseTimes` | `bool\|null` | no | Load Origin Response Times |
| `$loadOriginTraffic` | `bool\|null` | no | Load Origin Traffic |
| `$loadRequestsServed` | `bool\|null` | no | Load Requests Served |
| `$loadBandwidthUsed` | `bool\|null` | no | Load Bandwidth Used |
| `$loadOriginShieldBandwidth` | `bool\|null` | no | Load Origin Shield Bandwidth |
| `$loadGeographicTrafficDistribution` | `bool\|null` | no | Load Geographic Traffic Distribution |
| `$loadUserBalanceHistory` | `bool\|null` | no | Load User Balance History |

## Returns

`Statistics`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->statistics->get();
```
