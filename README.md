Install using composer :
```
composer require pierreminiggio/github-action-run-creator
```

```php
use PierreMiniggio\GithubActionRunCreator\GithubActionRunCreator;

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$creator = new GithubActionRunCreator();
$creator->create(
    'token',
    'pierreminiggio',
    'remotion-test-github-action',
    'render-video.yml',
    [
        'titleText' => 'Hello from PHP',
        'titleColor' => 'orange'
    ]
);
```
