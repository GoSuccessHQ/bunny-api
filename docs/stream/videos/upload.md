# `$stream->videos->upload()`

> Stream API · `PUT /library/{libraryId}/videos/{videoId}`

Upload the file of a video created with create().

The file is sent as the raw request body and streamed, so it does not have to fit into memory. The HTTP API cannot resume an interrupted upload; for files over 2 GB or unstable connections, bunny.net recommends TUS resumable uploads (see `TusUploader`).

## Signature

```php
public function upload(
    string $videoId,
    Stream|string $file,
    ?bool $jitEnabled = null,
    ?array $enabledResolutions = null,
    ?array $enabledOutputCodecs = null,
    ?bool $transcribeEnabled = null,
    ?array $transcribeLanguages = null,
    ?string $sourceLanguage = null,
    ?bool $generateTitle = null,
    ?bool $generateDescription = null,
    ?bool $generateChapters = null,
    ?bool $generateMoments = null,
): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$videoId` | `string` | yes | The GUID of the video. |
| `$file` | `string\|Stream` | yes | The video file, e.g. `Stream::fromFile('video.mp4')`. |
| `$jitEnabled` | `bool\|null` | no | Enable JIT encoding for this video (requires Premium Encoding); overrides the library settings. |
| `$enabledResolutions` | `list<string>\|null` | no | Resolutions to encode, e.g. `['720p', '1080p']`; overrides the library settings. |
| `$enabledOutputCodecs` | `list<string>\|null` | no | Codecs to encode with, e.g. `['x264', 'vp9']`; overrides the library settings. |
| `$transcribeEnabled` | `bool\|null` | no | Transcribe the video; this incurs transcription charges. |
| `$transcribeLanguages` | `list<string>\|null` | no | Target languages of the transcription as ISO 639-1 codes. |
| `$sourceLanguage` | `string\|null` | no | The language spoken in the video as ISO 639-1 code. |
| `$generateTitle` | `bool\|null` | no | Generate the title from the transcription. |
| `$generateDescription` | `bool\|null` | no | Generate the description from the transcription. |
| `$generateChapters` | `bool\|null` | no | Generate chapters from the transcription. |
| `$generateMoments` | `bool\|null` | no | Generate moments from the transcription. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Http\Stream;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$stream->videos->upload(videoId: '00000000-0000-0000-0000-000000000000', file: Stream::fromFile('path/to/file'));
```
