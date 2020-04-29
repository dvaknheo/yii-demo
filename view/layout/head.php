<?php

use Yiisoft\Yii\Bootstrap4\Nav;
use Yiisoft\Yii\Bootstrap4\NavBar;
use MY\Base\Helper\ViewHelper as V;

$login="???";
$user_id=null;

$t=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
$currentUrl='';
NavBar::counter(0);
if($t==='/'){
    NavBar::counter(1);
}
if($t==='/user'){
    NavBar::counter(1);
}
NavBar::counter(1);
?><!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Yii Demo</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"></head>
<body>
<?php

echo NavBar::begin()
      ->brandLabel('Yii Demo')
      ->brandUrl(V::URL('/'))
      ->options(['class' => 'navbar navbar-light bg-light navbar-expand-sm text-white'])
      ->start();
echo Nav::widget()
        ->currentPath($currentUrl ?? '')
        ->options(['class' => 'navbar-nav mr-auto'])
        ->items(
            [
                ['label' => 'Blog', 'url' => V::URL('/blog')],
                ['label' => 'Users', 'url' => V::URL('/user')],
                ['label' => 'Contact', 'url' => V::URL('/contact')],
            ]
        );
echo Nav::widget()
        ->currentPath($currentUrl ?? '')
        ->options(['class' => 'navbar-nav'])
        ->items(
            $user_id === null
                ? [
                ['label' => 'Login', 'url' => V::URL('/login')],
                ['label' => 'Signup', 'url' => V::URL('/signup')],
            ]
                : [['label' => "Logout ({$login})", 'url' => V::URL('/logout')]],
        );
echo NavBar::end();

?><main role="main" class="container py-4"><?php
