# `$bunny->shield->metrics->uploadScanning()`

> Shield API · `GET /shield/metrics/shield-zone/{shieldZoneId}/upload-scanning`

Get upload scanning metrics for the specified Shield Zone

## Signature

```php
public function uploadScanning(int $shieldZoneId): UploadScanningMetrics
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes | The ID of the Shield Zone. |

## Returns

`UploadScanningMetrics`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->metrics->uploadScanning(shieldZoneId: 123);
```
