# `$bunny->shield->zones->defaults()`

> Shield API · `GET /shield/shield-zone/defaults`

Get the recommended defaults for creating a Shield Zone

## Signature

```php
public function defaults(): ShieldZoneDefaults
```

## Returns

`ShieldZoneDefaults`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->zones->defaults();
```
