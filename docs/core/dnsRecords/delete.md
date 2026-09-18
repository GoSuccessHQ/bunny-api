# `$bunny->core->dnsRecords->delete()`

> Core Platform API · `DELETE /dnszone/{zoneId}/records/{id}`

Delete DNS Record

## Signature

```php
public function delete(int $zoneId, int $id): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$zoneId` | `int` | yes | The DNS Zone ID that contains the record. |
| `$id` | `int` | yes | The ID of the DNS record that will be deleted. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->dnsRecords->delete(zoneId: 123, id: 123);
```
