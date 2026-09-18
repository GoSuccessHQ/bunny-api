# `pullZones->completeExternalHttpCertificate()`

> Core Platform API · `POST /pullzone/completeExternalHttpCertificate`

Complete External HTTP Certificate

## Signature

```php
public function completeExternalHttpCertificate(string $hostname): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$hostname` | `string` | yes |  |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->pullZones->completeExternalHttpCertificate(hostname: 'cdn.example.com');
```
