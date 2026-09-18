# `$bunny->core->dnsZones->checkAvailability()`

> Core Platform API · `POST /dnszone/checkavailability`

Check whether a zone name is still available.

## Signature

```php
public function checkAvailability(string $name): bool
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$name` | `string` | yes | The zone name to check. |

## Returns

`bool`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->dnsZones->checkAvailability(name: 'example');
```
