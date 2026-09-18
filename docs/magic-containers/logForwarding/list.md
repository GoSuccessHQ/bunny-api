# `$bunny->magicContainers->logForwarding->list()`

> Magic Containers API · `GET /log/forwarding`

List log-forwarding configurations

Get a list of all log-forwarding configurations for the authenticated user.

## Signature

```php
public function list(): array
```

## Returns

`list<LogForwardingConfiguration>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->logForwarding->list();
```
