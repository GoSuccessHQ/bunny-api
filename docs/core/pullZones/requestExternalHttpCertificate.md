# `$bunny->core->pullZones->requestExternalHttpCertificate()`

> Core Platform API · `POST /pullzone/requestExternalHttpCertificate`

Request External HTTP Certificate

## Signature

```php
public function requestExternalHttpCertificate(string $hostname): mixed
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$hostname` | `string` | yes |  |

## Returns

`mixed`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->pullZones->requestExternalHttpCertificate(hostname: 'cdn.example.com');
```
