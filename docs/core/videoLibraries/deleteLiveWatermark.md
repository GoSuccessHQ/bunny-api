# `$bunny->core->videoLibraries->deleteLiveWatermark()`

> Core Platform API · `DELETE /videolibrary/{id}/live/watermark`

Delete Live Watermark

## Signature

```php
public function deleteLiveWatermark(int $id): void
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

$bunny->core->videoLibraries->deleteLiveWatermark(id: 123);
```
