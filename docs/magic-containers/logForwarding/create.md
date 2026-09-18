# `$bunny->magicContainers->logForwarding->create()`

> Magic Containers API · `POST /log/forwarding`

Create log forwarding configuration

Create a new log forwarding configuration.

## Signature

```php
public function create(CreateLogForwardingRequest $configuration): LogForwardingConfiguration
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$configuration` | `CreateLogForwardingRequest` | yes |  |

## Returns

`LogForwardingConfiguration`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\MagicContainers\Model\CreateLogForwardingRequest;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->logForwarding->create(configuration: new CreateLogForwardingRequest(/* ... */));
```
