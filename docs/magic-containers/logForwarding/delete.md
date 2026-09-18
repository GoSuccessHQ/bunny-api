# `$bunny->magicContainers->logForwarding->delete()`

> Magic Containers API · `DELETE /log/forwarding/{appId}`

Delete log-forwarding configuration

Delete a log-forwarding configuration.

## Signature

```php
public function delete(string $appId): void
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes | App ID for log forwarding configuration. |

## Returns

`void`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$bunny->magicContainers->logForwarding->delete(appId: '00000000-0000-0000-0000-000000000000');
```
