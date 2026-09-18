# `dnsZones->get()`

> Core Platform API · `GET /dnszone/{id}`

Get DNS Zone

## Signature

```php
public function get(int $id, ?DnsViewType $view = null): DnsZone
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the DNS Zone that will be returned |
| `$view` | `DnsViewType\|null` | no |  |

## Returns

`DnsZone`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->dnsZones->get(id: 123);
```
