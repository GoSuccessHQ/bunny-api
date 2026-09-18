# `dnsZones->update()`

> Core Platform API · `POST /dnszone/{id}`

Update DNS Zones

## Signature

```php
public function update(int $id, DnsZoneUpdate $changes): DnsZone
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the DNS Zone that will be updated |
| `$changes` | `DnsZoneUpdate` | yes |  |

## Returns

`DnsZone`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Core\Model\DnsZoneUpdate;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->dnsZones->update(id: 123, changes: new DnsZoneUpdate(/* ... */));
```
