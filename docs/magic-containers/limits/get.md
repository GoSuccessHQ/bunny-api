# `$bunny->magicContainers->limits->get()`

> Magic Containers API · `GET /limits`

Get User Limits

Retrieves the current resource limits and usage for the authenticated user, including application counts and instance limits.

## Signature

```php
public function get(): UserLimits
```

## Returns

`UserLimits`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->magicContainers->limits->get();
```
