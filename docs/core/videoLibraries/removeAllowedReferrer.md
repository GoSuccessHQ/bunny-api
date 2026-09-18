# `$bunny->core->videoLibraries->removeAllowedReferrer()`

> Core Platform API · `POST /videolibrary/{id}/removeAllowedReferrer`

Remove Allowed Referer

## Signature

```php
public function removeAllowedReferrer(int $id, string $hostname): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the requested Video Library |
| `$hostname` | `string` | yes | The hostname that will be removed as an allowed referer |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->videoLibraries->removeAllowedReferrer(id: 123, hostname: 'cdn.example.com');
```
