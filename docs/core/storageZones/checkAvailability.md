# `$bunny->core->storageZones->checkAvailability()`

> Core Platform API · `POST /storagezone/checkavailability`

Check whether a storage zone name is still available.

## Signature

```php
public function checkAvailability(string $name): bool
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$name` | `string` | yes | The storage zone name to check. |

## Returns

`bool`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->storageZones->checkAvailability(name: 'example');
```
