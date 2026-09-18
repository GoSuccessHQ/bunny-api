# `$bunny->edgeScripting->scripts->delete()`

> Edge Scripting API · `DELETE /compute/script/{id}`

Delete Edge Script

## Signature

```php
public function delete(int $id, ?bool $deleteLinkedPullZones = null): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the script that will be deleted. |
| `$deleteLinkedPullZones` | `bool\|null` | no | Deletes all pull zones linked to this edge script if true, otherwise changes origin to landing page |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->edgeScripting->scripts->delete(id: 123);
```
