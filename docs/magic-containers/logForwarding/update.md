# `$bunny->magicContainers->logForwarding->update()`

> Magic Containers API · `PUT /log/forwarding/{appId}`

Update log-forwarding configuration

Update an existing log-forwarding configuration.

## Signature

```php
public function update(string $appId, CreateLogForwardingRequest $configuration): LogForwardingConfiguration
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$appId` | `string` | yes | App ID for log forwarding configuration. |
| `$configuration` | `CreateLogForwardingRequest` | yes |  |

## Returns

`LogForwardingConfiguration`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\MagicContainers\Model\CreateLogForwardingRequest;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->logForwarding->update(appId: '00000000-0000-0000-0000-000000000000', configuration: new CreateLogForwardingRequest(/* ... */));
```
