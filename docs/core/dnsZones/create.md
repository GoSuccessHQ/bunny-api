# `$bunny->core->dnsZones->create()`

> Core Platform API · `POST /dnszone`

Add DNS Zone

## Signature

```php
public function create(string $domain, ?array $records = null): DnsZone
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$domain` | `string` | yes | The domain that will be added. |
| `$records` | `list<DnsRecordCreate>\|null` | no | Optional array of DNS records to add when creating the zone. |

## Returns

`DnsZone`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->dnsZones->create(domain: 'cdn.example.com');
```
