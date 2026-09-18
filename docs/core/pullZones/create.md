# `pullZones->create()`

> Core Platform API · `POST /pullzone`

Add Pull Zone

## Signature

```php
public function create(PullZoneCreate $pullZone): PullZone
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$pullZone` | `PullZoneCreate` | yes |  |

## Returns

`PullZone`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Core\Model\PullZoneCreate;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->pullZones->create(pullZone: new PullZoneCreate(/* ... */));
```
