# `dnsRecords->create()`

> Core Platform API · `PUT /dnszone/{zoneId}/records`

Add DNS Record

## Signature

```php
public function create(int $zoneId, DnsRecordCreate $record): DnsRecord
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$zoneId` | `int` | yes | The DNS Zone ID to which the record will be added. |
| `$record` | `DnsRecordCreate` | yes |  |

## Returns

`DnsRecord`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Core\Model\DnsRecordCreate;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->dnsRecords->create(zoneId: 123, record: new DnsRecordCreate(/* ... */));
```
