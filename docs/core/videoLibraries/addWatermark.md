# `videoLibraries->addWatermark()`

> Core Platform API · `PUT /videolibrary/{id}/watermark`

Upload the watermark image of a video library.

## Signature

```php
public function addWatermark(int $id, Stream|string $image, string $contentType = 'image/png'): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the video library |
| `$image` | `string\|Stream` | yes | The image file. |
| `$contentType` | `string` | no | The image's media type. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Http\Stream;

$bunny = new Bunny('your-api-key');

$bunny->core->videoLibraries->addWatermark(id: 123, image: Stream::fromFile('path/to/file'));
```
