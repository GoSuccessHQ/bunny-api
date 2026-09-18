# `$stream->videos->deleteCaption()`

> Stream API · `DELETE /library/{libraryId}/videos/{videoId}/captions/{srclang}`

Delete Caption

## Signature

```php
public function deleteCaption(string $videoId, string $srclang): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$videoId` | `string` | yes | The GUID of the video. |
| `$srclang` | `string` | yes | The srclang shortcode of the caption to delete, e.g. "en". |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$stream->videos->deleteCaption(videoId: '00000000-0000-0000-0000-000000000000', srclang: 'example');
```
