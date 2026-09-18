# `$bunny->core->pullZones->removeBlockedIp()`

> Core Platform API · `POST /pullzone/{id}/removeBlockedIp`

Remove Blocked IP

## Signature

```php
public function removeBlockedIp(int $id, string $blockedIp): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the requested Pull Zone |
| `$blockedIp` | `string` | yes | The IP that will be removed fromt he block list |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->pullZones->removeBlockedIp(id: 123, blockedIp: '203.0.113.10');
```
