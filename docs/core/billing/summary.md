# `billing->summary()`

> Core Platform API · `GET /billing/summary`

Get Billing Summary

## Signature

```php
public function summary(): array
```

## Returns

`list<BillingSummaryItem>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->billing->summary();
```
