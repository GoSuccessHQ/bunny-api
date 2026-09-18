# `$bunny->core->pullZones->addAllowedReferrer()`

> Core Platform API · `POST /pullzone/{id}/addAllowedReferrer`

Add Allowed Referer

## Signature

```php
public function addAllowedReferrer(int $id, string $hostname): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the requested Pull Zone |
| `$hostname` | `string` | yes | The hostname that will be added as an allowed referer |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->pullZones->addAllowedReferrer(id: 123, hostname: 'cdn.example.com');
```
