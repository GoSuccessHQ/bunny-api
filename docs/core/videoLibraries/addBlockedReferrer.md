# `$bunny->core->videoLibraries->addBlockedReferrer()`

> Core Platform API · `POST /videolibrary/{id}/addBlockedReferrer`

Add Blocked Referer

## Signature

```php
public function addBlockedReferrer(int $id, string $hostname): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the requested Video Library |
| `$hostname` | `string` | yes | The hostname that will be added as a blocked referer |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->videoLibraries->addBlockedReferrer(id: 123, hostname: 'cdn.example.com');
```
