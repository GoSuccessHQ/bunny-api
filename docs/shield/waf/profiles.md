# `$bunny->shield->waf->profiles()`

> Shield API · `GET /shield/waf/profiles`

Retrieve all available WAF profiles

## Signature

```php
public function profiles(): array
```

## Returns

`list<list<WafProfile>>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->waf->profiles();
```
