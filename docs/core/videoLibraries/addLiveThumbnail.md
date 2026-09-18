# `videoLibraries->addLiveThumbnail()`

> Core Platform API · `PUT /videolibrary/{id}/live/thumbnail`

Upload the thumbnail shown for live streams of a video library.

## Signature

```php
public function addLiveThumbnail(int $id, Stream|string $image, string $contentType = 'image/png'): void
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

$bunny->core->videoLibraries->addLiveThumbnail(id: 123, image: Stream::fromFile('path/to/file'));
```
