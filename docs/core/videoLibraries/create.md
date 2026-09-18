# `videoLibraries->create()`

> Core Platform API · `POST /videolibrary`

Add Video Library

## Signature

```php
public function create(VideoLibraryCreate $library): VideoLibrary
```

## Parameters

| Name | Type | Required | Description |
| --- | --- | --- | --- |
| `$library` | `VideoLibraryCreate` | yes |  |

## Returns

`VideoLibrary`

## Example

```php
use GoSuccess\Bunny\Bunny;
use GoSuccess\Bunny\Core\Model\VideoLibraryCreate;

$bunny = new Bunny('your-api-key');

$result = $bunny->core->videoLibraries->create(library: new VideoLibraryCreate(/* ... */));
```
