# `$bunny->core->loadBalancers->accountStatistics()`

> Core Platform API · `GET /loadbalancer/statistics`

Get statistics roll-ups for all Load Balancers on the account

## Signature

```php
public function accountStatistics(?DateTimeInterface $from = null, ?DateTimeInterface $to = null): array
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$from` | `DateTimeInterface\|null` | no |  |
| `$to` | `DateTimeInterface\|null` | no |  |

## Returns

`list<LoadBalancerAccountSummary>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->loadBalancers->accountStatistics();
```
