# `$bunny->core->loadBalancers->statistics()`

> Core Platform API · `GET /loadbalancer/{loadBalancerId}/statistics`

Get Load Balancer statistics

## Signature

```php
public function statistics(
    int $loadBalancerId,
    ?DateTimeInterface $from = null,
    ?DateTimeInterface $to = null,
    ?bool $hourly = null,
    ?bool $exactRange = null,
): LoadBalancerStatistics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$loadBalancerId` | `int` | yes |  |
| `$from` | `DateTimeInterface\|null` | no | (Optional) The start date of the statistics. If no value is passed, the last 24 hours will be returned. Rounded to full-day boundaries unless hourly=true and exactRange=true. |
| `$to` | `DateTimeInterface\|null` | no | (Optional) The end date of the statistics. Rounded to full-day boundaries unless hourly=true and exactRange=true. |
| `$hourly` | `bool\|null` | no | (Optional) If true, the statistics will be returned in hourly grouping. Hourly statistics are available for the last 30 days only. |
| `$exactRange` | `bool\|null` | no | (Optional) If true and hourly=true, the exact hour components of from and to will be preserved instead of rounding to full-day boundaries. |

## Returns

`LoadBalancerStatistics`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->loadBalancers->statistics(loadBalancerId: 123);
```
