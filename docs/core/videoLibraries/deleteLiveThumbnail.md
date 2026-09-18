# `$bunny->core->videoLibraries->deleteLiveThumbnail()`

> Core Platform API · `DELETE /videolibrary/{id}/live/thumbnail`

Delete Live Thumbnail

## Signature

```php
public function deleteLiveThumbnail(int $id): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the requested Video Library |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->videoLibraries->deleteLiveThumbnail(id: 123);
```
