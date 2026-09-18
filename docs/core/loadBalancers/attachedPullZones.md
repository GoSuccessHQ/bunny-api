# `$bunny->core->loadBalancers->attachedPullZones()`

> Core Platform API · `GET /loadbalancer/{loadBalancerId}/pullzones`

Get the pull zones attached to a Load Balancer

## Signature

```php
public function attachedPullZones(
    int $loadBalancerId,
    ?DateTimeInterface $from = null,
    ?DateTimeInterface $to = null,
): array
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$loadBalancerId` | `int` | yes |  |
| `$from` | `DateTimeInterface\|null` | no |  |
| `$to` | `DateTimeInterface\|null` | no |  |

## Returns

`list<LoadBalancerAttachedZone>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->loadBalancers->attachedPullZones(loadBalancerId: 123);
```
