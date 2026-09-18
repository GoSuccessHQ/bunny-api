# `$bunny->shield->uploadScanning->update()`

> Shield API · `PATCH /shield/shield-zone/{shieldZoneId}/upload-scanning`

Update your Upload Scanning Configuration

## Signature

```php
public function update(
    int $shieldZoneId,
    ?bool $isEnabled = null,
    ?UploadScanningScannerMode $csamScanningMode = null,
    ?UploadScanningScannerMode $antivirusScanningMode = null,
): UploadScanningConfiguration
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes |  |
| `$isEnabled` | `bool\|null` | no |  |
| `$csamScanningMode` | `UploadScanningScannerMode\|null` | no | 0 = Disabled 1 = Log 2 = Block |
| `$antivirusScanningMode` | `UploadScanningScannerMode\|null` | no | 0 = Disabled 1 = Log 2 = Block |

## Returns

`UploadScanningConfiguration`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->uploadScanning->update(shieldZoneId: 123);
```
