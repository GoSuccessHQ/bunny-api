# `$bunny->core->billing->summaryPdf()`

> Core Platform API · `GET /billing/summary/{billingRecordId}/pdf`

Download the PDF summary of a billing record.

## Signature

```php
public function summaryPdf(int $billingRecordId): string
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$billingRecordId` | `int` | yes | The ID of the billing record, see `BillingResource::details()`. |

## Returns

`string`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->billing->summaryPdf(billingRecordId: 123);
```
