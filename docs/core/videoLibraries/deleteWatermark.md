# `videoLibraries->deleteWatermark()`

> Core Platform API · `DELETE /videolibrary/{id}/watermark`

Delete Watermark

## Signature

```php
public function deleteWatermark(int $id): void
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

$bunny->core->videoLibraries->deleteWatermark(id: 123);
```
