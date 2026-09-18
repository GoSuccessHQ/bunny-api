# `$stream->videos->create()`

> Stream API · `POST /library/{libraryId}/videos`

Create Video

## Signature

```php
public function create(
    string $title,
    ?string $collectionId = null,
    ?int $thumbnailTime = null,
): Video
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$title` | `string` | yes | The title of the video |
| `$collectionId` | `string\|null` | no | The ID of the collection where the video will be put |
| `$thumbnailTime` | `int\|null` | no | Video time in ms to extract the main video thumbnail. |

## Returns

`Video`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$result = $stream->videos->create(title: 'example');
```
