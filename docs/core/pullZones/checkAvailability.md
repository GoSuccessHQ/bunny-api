# `pullZones->checkAvailability()`

> Core Platform API · `POST /pullzone/checkavailability`

Check whether a pull zone name is still available.

## Signature

```php
public function checkAvailability(string $name): bool
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$name` | `string` | yes | The pull zone name to check. |

## Returns

`bool`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->pullZones->checkAvailability(name: 'example');
```
