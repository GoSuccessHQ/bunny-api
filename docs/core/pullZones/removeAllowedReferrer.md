# `$bunny->core->pullZones->removeAllowedReferrer()`

> Core Platform API · `POST /pullzone/{id}/removeAllowedReferrer`

Remove Allowed Referer

## Signature

```php
public function removeAllowedReferrer(int $id, string $hostname): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the requested Pull Zone |
| `$hostname` | `string` | yes | The hostname that will be removed as an allowed referer |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->pullZones->removeAllowedReferrer(id: 123, hostname: 'cdn.example.com');
```
