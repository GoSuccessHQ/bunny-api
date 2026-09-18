# `$bunny->core->pullZones->addBlockedIp()`

> Core Platform API · `POST /pullzone/{id}/addBlockedIp`

Add Blocked IP

## Signature

```php
public function addBlockedIp(int $id, string $blockedIp): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the requested Pull Zone |
| `$blockedIp` | `string` | yes | The IP that will be blocked from accessing the pull zone |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->pullZones->addBlockedIp(id: 123, blockedIp: '203.0.113.10');
```
