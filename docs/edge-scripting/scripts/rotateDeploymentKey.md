# `$bunny->edgeScripting->scripts->rotateDeploymentKey()`

> Edge Scripting API · `POST /compute/script/{id}/deploymentKey/rotate`

Rotate Deployment Key

## Signature

```php
public function rotateDeploymentKey(int $id): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$id` | `int` | yes | The ID of the edge script that should have deployment key rotated |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->edgeScripting->scripts->rotateDeploymentKey(id: 123);
```
