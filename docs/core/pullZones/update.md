# `$bunny->core->pullZones->update()`

> Core Platform API · `POST /pullzone/{id}`

Update Pull Zone

## Signature

```php
public function update(int $id, PullZoneUpdate $changes): PullZone
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the Pull Zone that should be updated |
| `$changes` | `PullZoneUpdate` | yes |  |

## Returns

`PullZone`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Core\Model\PullZoneUpdate;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->pullZones->update(id: 123, changes: new PullZoneUpdate(/* ... */));
```
