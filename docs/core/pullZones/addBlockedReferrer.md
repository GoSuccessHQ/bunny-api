# `pullZones->addBlockedReferrer()`

> Core Platform API · `POST /pullzone/{id}/addBlockedReferrer`

Add Blocked Referer

## Signature

```php
public function addBlockedReferrer(int $id, string $hostname): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the requested Pull Zone |
| `$hostname` | `string` | yes | The hostname that will be added as a blocked referer |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->pullZones->addBlockedReferrer(id: 123, hostname: 'cdn.example.com');
```
