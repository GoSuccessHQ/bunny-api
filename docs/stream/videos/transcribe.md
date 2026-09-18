# `$stream->videos->transcribe()`

> Stream API · `POST /library/{libraryId}/videos/{videoId}/transcribe`

Transcribe video

Queues transcription for the video, generating captions. By default this is blocked if the video was already auto-transcribed; set force to true to re-queue (and re-bill) transcription anyway.

## Signature

```php
public function transcribe(
    string $videoId,
    ?bool $force = null,
    ?array $targetLanguages = null,
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
| `$force` | `bool\|null` | no | If set to true, re-queues transcription even if the video was already auto-transcribed. This re-bills transcription usage. |
| `$targetLanguages` | `list<string>\|null` | no | List of languages that will be used as target languages, use ISO 639-1 language codes. |
| `$generateTitle` | `bool\|null` | no | Whether video title should be automatically generated. |
| `$generateDescription` | `bool\|null` | no | Whether video description should be automatically generated. |
| `$generateChapters` | `bool\|null` | no | Whether video chapters should be automatically generated. |
| `$generateMoments` | `bool\|null` | no | Whether video moments should be automatically generated. |
| `$sourceLanguage` | `string\|null` | no | Video source language, use ISO 639-1 language code. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$stream->videos->transcribe(videoId: '00000000-0000-0000-0000-000000000000');
```
