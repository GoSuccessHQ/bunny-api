# `videoLibraries->languages()`

> Core Platform API · `GET /videolibrary/languages`

Get Languages

## Signature

```php
public function languages(): array
```

## Returns

`list<VideoLibraryLanguage>`

## Example

```php
use GoSuccess\Bunny\Bunny;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->videoLibraries->languages();
```
