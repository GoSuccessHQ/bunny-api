# `pullZones->resetSecurityKey()`

> Core Platform API · `POST /pullzone/{id}/resetSecurityKey`

Reset Token Key

## Signature

```php
public function resetSecurityKey(int $id, ?string $securityKey = null): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the requested Pull Zone |
| `$securityKey` | `string\|null` | no |  |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->pullZones->resetSecurityKey(id: 123);
```
