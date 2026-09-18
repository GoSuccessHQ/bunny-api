# `billing->paymentRequests()`

> Core Platform API · `GET /billing/payment-requests`

Get Pending Payment Requests

## Signature

```php
public function paymentRequests(): array
```

## Returns

`list<PaymentRequest>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->billing->paymentRequests();
```
