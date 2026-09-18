# `$bunny->core->pullZones->loadFreeCertificate()`

> Core Platform API · `GET /pullzone/loadFreeCertificate`

Load Free Certificate

Despite being a GET request, this issues a certificate and therefore changes state.

## Signature

```php
public function loadFreeCertificate(string $hostname, ?bool $useOnlyHttp01 = null): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$hostname` | `string` | yes | The hostname that the certificate will be loaded for |
| `$useOnlyHttp01` | `bool\|null` | no | If false and a Bunny DNS Zone exists for the domain, DNS01 validation we be attempted. This has no effect on wildcard domains, as this can only use DNS01 |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->pullZones->loadFreeCertificate(hostname: 'cdn.example.com');
```
