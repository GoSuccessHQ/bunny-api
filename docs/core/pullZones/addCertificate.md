# `pullZones->addCertificate()`

> Core Platform API · `POST /pullzone/{id}/addCertificate`

Add Custom Certificate

## Signature

```php
public function addCertificate(int $id, string $hostname, string $certificate, string $certificateKey): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the requested Pull Zone |
| `$hostname` | `string` | yes | The hostname to which the hostname will be added |
| `$certificate` | `string` | yes | The Base64 encoded binary data of the certificate file |
| `$certificateKey` | `string` | yes | The Base64 encoded binary data of the certificate key file |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->pullZones->addCertificate(id: 123, hostname: 'cdn.example.com', certificate: 'example', certificateKey: 'example');
```
