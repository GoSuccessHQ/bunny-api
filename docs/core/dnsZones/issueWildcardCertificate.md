# `dnsZones->issueWildcardCertificate()`

> Core Platform API · `POST /dnszone/{zoneId}/certificate/issue`

Issue new wildcard certificate

## Signature

```php
public function issueWildcardCertificate(int $zoneId, ?string $domain = null): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$zoneId` | `int` | yes | The DNS Zone ID requiring a new certificate. |
| `$domain` | `string\|null` | no |  |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->dnsZones->issueWildcardCertificate(zoneId: 123);
```
