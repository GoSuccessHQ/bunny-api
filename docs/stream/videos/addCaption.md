# `$stream->videos->addCaption()`

> Stream API · `POST /library/{libraryId}/videos/{videoId}/captions/{srclang}`

Add or replace the captions of one language.

The API expects the captions file base64-encoded; this method encodes it.

## Signature

```php
public function addCaption(
    string $videoId,
    string $language,
    Stream|string $captions,
    ?string $label = null,
): ?CaptionValidation
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$videoId` | `string` | yes | The GUID of the video. |
| `$language` | `string` | yes | The language code of the captions, e.g. `en`. |
| `$captions` | `string\|Stream` | yes | The captions file, e.g. WebVTT. |
| `$label` | `string\|null` | no | The label the player shows, e.g. `English`. |

## Returns

`CaptionValidation|null`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Http\Stream;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$result = $stream->videos->addCaption(videoId: '00000000-0000-0000-0000-000000000000', language: 'example', captions: Stream::fromFile('path/to/file'));
```
