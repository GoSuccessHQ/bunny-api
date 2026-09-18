# `$bunny->shield->waf->enums()`

> Shield API · `GET /shield/waf/enums`

Retrieve all available WAF enum mappings

## Signature

```php
public function enums(): array
```

## Returns

`list<WafEnum>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->waf->enums();
```
