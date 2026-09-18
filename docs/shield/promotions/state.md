# `$bunny->shield->promotions->state()`

> Shield API · `GET /shield/promo/state`

Get the Shield promotions of the account.

## Signature

```php
public function state(): PromotionState
```

## Returns

`PromotionState`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->promotions->state();
```
