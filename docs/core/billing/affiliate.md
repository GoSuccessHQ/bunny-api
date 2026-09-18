# `billing->affiliate()`

> Core Platform API · `GET /billing/affiliate`

Get affiliate details

## Signature

```php
public function affiliate(): AffiliateDetails
```

## Returns

`AffiliateDetails`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->billing->affiliate();
```
