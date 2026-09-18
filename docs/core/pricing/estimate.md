# `pricing->estimate()`

> Core Platform API · `GET /v1/pricing/{source}/{resourceId}`

Get active price for a resource and optionally cost estimate if usage amount provided

## Signature

```php
public function estimate(PricingEstimationSource $source, int $resourceId, ?int $units = null): PriceEstimation
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$source` | `PricingEstimationSource` | yes | The billing source to estimate pricing for |
| `$resourceId` | `int` | yes | The ID of the resource (pull zone, storage zone, video library, or edge script) |
| `$units` | `int\|null` | no | Optional number of units to calculate estimated total cost |

## Returns

`PriceEstimation`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Core\Enum\PricingEstimationSource;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->pricing->estimate(source: PricingEstimationSource::CDN_Standard_Tier_EU_Traffic, resourceId: 123);
```
