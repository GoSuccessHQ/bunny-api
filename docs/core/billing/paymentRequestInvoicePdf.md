# `billing->paymentRequestInvoicePdf()`

> Core Platform API · `GET /billing/payment-request-invoice/{id}/pdf`

Download the invoice of a payment request as PDF.

## Signature

```php
public function paymentRequestInvoicePdf(int $id): string
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the payment request, see {@see BillingResource::paymentRequests()}. |

## Returns

`string`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->billing->paymentRequestInvoicePdf(id: 123);
```
