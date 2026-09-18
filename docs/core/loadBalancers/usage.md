# `$bunny->core->loadBalancers->usage()`

> Core Platform API · `GET /loadbalancer/{loadBalancerId}/usage`

Get current-period request usage and pricing for a Load Balancer

## Signature

```php
public function usage(int $loadBalancerId, ?int $year = null, ?int $month = null): LoadBalancerUsage
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$loadBalancerId` | `int` | yes |  |
| `$year` | `int\|null` | no | Year of the usage month (UTC). Defaults to the current year; supply together with month. |
| `$month` | `int\|null` | no | Month of the usage month, 1-12 (UTC). Defaults to the current month; supply together with year. |

## Returns

`LoadBalancerUsage`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->loadBalancers->usage(loadBalancerId: 123);
```
