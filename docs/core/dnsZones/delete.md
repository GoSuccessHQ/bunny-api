# `$bunny->core->dnsZones->delete()`

> Core Platform API · `DELETE /dnszone/{id}`

Delete DNS Zone

## Signature

```php
public function delete(int $id): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The DNS Zone ID that will be deleted. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->core->dnsZones->delete(id: 123);
```
