<?php

use App\Asset\AppAsset;
use App\Widget\PerformanceMetrics;
use Yiisoft\Yii\Bootstrap4\Nav;
use Yiisoft\Yii\Bootstrap4\NavBar;

/**
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 * @var \App\Entity\User $user
 * @var \Yiisoft\Assets\AssetManager $assetManager
 * @var string $content
 * @var null|string $currentUrl
 */

$assetManager->register([
    AppAsset::class
]);

$this->setCssFiles($assetManager->getCssFiles());
$this->setJsFiles($assetManager->getJsFiles());

$this->beginPage();
?><!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yii Demo</title>
    <?php $this->head() ?>
</head>
<body>
<?php
$this->beginBody();

echo NavBar::begin()
      ->brandLabel('Yii Demo')
      ->brandUrl(V::URL('site/index'))
      ->options(['class' => 'navbar navbar-light bg-light navbar-expand-sm text-white'])
      ->start();
echo Nav::widget()
        ->currentPath($currentUrl ?? '')
        ->options(['class' => 'navbar-nav mr-auto'])
        ->items(
            [
                ['label' => 'Blog', 'url' => V::URL('blog/index')],
                ['label' => 'Users', 'url' => V::URL('user/index')],
                ['label' => 'Contact', 'url' => V::URL('site/contact')],
            ]
        );
echo Nav::widget()
        ->currentPath($currentUrl ?? '')
        ->options(['class' => 'navbar-nav'])
        ->items(
            $user->getId() === null
                ? [
                ['label' => 'Login', 'url' => V::URL('site/login')],
                ['label' => 'Signup', 'url' => V::URL('site/signup')],
            ]
                : [['label' => "Logout ({$user->getLogin()})", 'url' => V::URL('site/logout')]],
        );
echo NavBar::end();

?><main role="main" class="container py-4"><?php
echo $content;
?></main>

<footer class="container py-4">
    <?= PerformanceMetrics::widget() ?>
</footer>
<?php

$this->endBody();
?>
</body>
</html>
<?php
$this->endPage(true);
