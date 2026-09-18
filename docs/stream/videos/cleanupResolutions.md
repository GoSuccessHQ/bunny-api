# `$stream->videos->cleanupResolutions()`

> Stream API · `POST /library/{libraryId}/videos/{videoId}/resolutions/cleanup`

Cleanup unconfigured resolutions

Deletes stored resolution files that are no longer configured for the library, or the specific resolutions passed in resolutionsToDelete/allResolutions. This cannot be undone. If the library has 'keep original files' turned off, the original source file is also deleted as part of this cleanup even when deleteOriginal is not set.

## Signature

```php
public function cleanupResolutions(
    string $videoId,
    ?string $resolutionsToDelete = null,
    ?bool $deleteNonConfiguredResolutions = null,
    ?bool $allResolutions = null,
    ?bool $deleteOriginal = null,
    ?string $outputs = null,
    ?bool $deleteMp4Files = null,
    ?bool $dryRun = null,
): ResolutionsCleanup
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$videoId` | `string` | yes | The GUID of the video. |
| `$resolutionsToDelete` | `string\|null` | no | Comma-separated list of specific resolutions to delete (e.g. "720p,480p"), instead of relying on deleteNonConfiguredResolutions/allResolutions. |
| `$deleteNonConfiguredResolutions` | `bool\|null` | no | If set to true, all resolutions not currently configured for the library will be deleted. |
| `$allResolutions` | `bool\|null` | no | If set to true, all resolutions for the video will be deleted, regardless of library configuration. When targeting HLS resolutions (the default, or outputs=all), at least one resolution must remain after cleanup or the request is rejected — scope outputs to mp4 to remove every rendition. |
| `$deleteOriginal` | `bool\|null` | no | If set to true, the original source file is deleted as well. Note: the original is also deleted when the library has 'keep original files' disabled, regardless of this flag. |
| `$outputs` | `string\|null` | no | Outputs to clean. Supported values: hls, mp4, all |
| `$deleteMp4Files` | `bool\|null` | no | If set to true, MP4 fallback files for the deleted resolutions are removed too. |
| `$dryRun` | `bool\|null` | no | If set to true, no actual file manipulation will happen, only informational data will be returned |

## Returns

`ResolutionsCleanup`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');
$stream = $bunny->stream(12345, 'library-api-key');

$result = $stream->videos->cleanupResolutions(videoId: '00000000-0000-0000-0000-000000000000');
```
