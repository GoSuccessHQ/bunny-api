# `dnsRecords->update()`

> Core Platform API · `POST /dnszone/{zoneId}/records/{id}`

Update DNS Record

## Signature

```php
public function update(int $zoneId, int $id, DnsRecordUpdate $changes): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$zoneId` | `int` | yes | The DNS Zone ID that contains the record. |
| `$id` | `int` | yes | The ID of the DNS record that will be updated. |
| `$changes` | `DnsRecordUpdate` | yes |  |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Core\Model\DnsRecordUpdate;

$bunny = new Bunny('your-api-key');

$bunny->core->dnsRecords->update(zoneId: 123, id: 123, changes: new DnsRecordUpdate(/* ... */));
```
