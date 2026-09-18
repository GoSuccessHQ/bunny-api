# `$bunny->shield->zones->pullZoneMapping()`

> Shield API · `GET /shield/shield-zones/pullzone-mapping`

Get Active Shield Zones for Pullzone Mapping

## Signature

```php
public function pullZoneMapping(): array
```

## Returns

`list<ShieldZonePullZoneMapping>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->zones->pullZoneMapping();
```
