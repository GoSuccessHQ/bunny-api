# `$bunny->core->billing->details()`

> Core Platform API · `GET /billing`

Get Billing Details

Get the billing status details

## Signature

```php
public function details(): BillingDetails
```

## Returns

`BillingDetails`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->billing->details();
```
