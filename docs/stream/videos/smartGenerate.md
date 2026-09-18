# `$stream->videos->smartGenerate()`

> Stream API · `POST /library/{libraryId}/videos/{videoId}/smart`

Trigger Smart actions

Queues Smart Actions (title, description, chapters and/or moments generation) for a video that already has captions. This endpoint does not check the video library's Smart Actions setting — requests are accepted, processed and billed for transcription usage even when Smart Actions is disabled for the library. Requires at least one of GenerateTitle, GenerateDescription, GenerateChapters or GenerateMoments, and returns 429 once the library's Smart Actions usage threshold for the video is exceeded.

## Signature

```php
public function smartGenerate(
    string $videoId,
    ?bool $generateTitle = null,
    ?bool $generateDescription = null,
    ?bool $generateChapters = null,
    ?bool $generateMoments = null,
    ?string $sourceLanguage = null,
): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$videoId` | `string` | yes | The GUID of the video. |
| `$generateTitle` | `bool\|null` | no | Whether video title should be generated. |
| `$generateDescription` | `bool\|null` | no | Whether video description should be generated. |
| `$generateChapters` | `bool\|null` | no | Whether video chapters should be generated. |
| `$generateMoments` | `bool\|null` | no | Whether video moments should be generated. |
| `$sourceLanguage` | `string\|null` | no | (Optional) Video source language, use ISO 639-1 language code. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$stream->videos->smartGenerate(videoId: '00000000-0000-0000-0000-000000000000');
```
