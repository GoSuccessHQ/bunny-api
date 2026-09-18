# `$bunny->shield->uploadScanning->get()`

> Shield API · `GET /shield/shield-zone/{shieldZoneId}/upload-scanning`

Get your Current Upload Scanning Configuration

## Signature

```php
public function get(int $shieldZoneId): UploadScanningConfiguration
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$shieldZoneId` | `int` | yes |  |

## Returns

`UploadScanningConfiguration`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->shield->uploadScanning->get(shieldZoneId: 123);
```
