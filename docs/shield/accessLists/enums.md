# `$bunny->shield->accessLists->enums()`

> Shield API · `GET /shield/shield-zone/{shieldZoneId}/access-lists/enums`

Get all Access Lists API enumeration types and their values

## Signature

```php
public function enums(int $shieldZoneId): array
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes |  |

## Returns

`array<array-key,`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->accessLists->enums(shieldZoneId: 123);
```
