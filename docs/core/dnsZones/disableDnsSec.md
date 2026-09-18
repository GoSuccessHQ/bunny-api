# `$bunny->core->dnsZones->disableDnsSec()`

> Core Platform API · `DELETE /dnszone/{id}/dnssec`

Disable DNSSEC on a DNS Zone

## Signature

```php
public function disableDnsSec(int $id): DnsSecDsRecord
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the DNS Zone for which DNSSEC will be disabled |

## Returns

`DnsSecDsRecord`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->dnsZones->disableDnsSec(id: 123);
```
