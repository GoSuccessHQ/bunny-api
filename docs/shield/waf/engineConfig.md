# `$bunny->shield->waf->engineConfig()`

> Shield API · `GET /shield/waf/engine-config`

Retrieve the default WAF engine configuration

## Signature

```php
public function engineConfig(): array
```

## Returns

`list<WafEngineSetting>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->waf->engineConfig();
```
