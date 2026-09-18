# `$bunny->magicContainers->logForwarding->get()`

> Magic Containers API · `GET /log/forwarding/{appId}`

Get log-forwarding configuration

Get a specific log-forwarding configuration by ID.

## Signature

```php
public function get(string $appId): LogForwardingConfiguration
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes | App ID for log forwarding configuration. |

## Returns

`LogForwardingConfiguration`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->logForwarding->get(appId: '00000000-0000-0000-0000-000000000000');
```
