# `pullZones->requestExternalDnsCertificate()`

> Core Platform API · `POST /pullzone/requestExternalDnsCertificate`

Request External DNS Certificate

## Signature

```php
public function requestExternalDnsCertificate(string $hostname): mixed
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

$result = $bunny->core->pullZones->requestExternalDnsCertificate(hostname: 'cdn.example.com');
```
