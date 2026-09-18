# `$bunny->core->dnsZones->enableDnsSec()`

> Core Platform API · `POST /dnszone/{id}/dnssec`

Enable DNSSEC on a DNS Zone

## Signature

```php
public function enableDnsSec(int $id): DnsSecDsRecord
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the DNS Zone for which DNSSEC will be enabled |

## Returns

`DnsSecDsRecord`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->dnsZones->enableDnsSec(id: 123);
```
