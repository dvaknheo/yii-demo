<?php

use Psr\Container\ContainerInterface;
use Yiisoft\Composer\Config\Builder;
use Yiisoft\Di\Container;
use Yiisoft\Http\Method;
use Yiisoft\Yii\Web\Application;
use Yiisoft\Yii\Web\SapiEmitter;
use Yiisoft\Yii\Web\ServerRequestFactory;

require_once dirname(__DIR__) . '/vendor/autoload.php';


// Don't do it in production, assembling takes it's time
Builder::rebuild(__DIR__);

$startTime = microtime(true);
$container = new Container(
    require Builder::path('web', dirname(__DIR__)),
    require Builder::path('providers', dirname(__DIR__))
);
$container = $container->get(ContainerInterface::class);

require_once dirname(__DIR__) . '/src/globals.php';

////[[[[
//*
$path = realpath(__DIR__.'/..');
$options=[];
$options['path'] = $path;
$options['handle_all_exception']=false;
$options['handle_all_dev_error']=false;

$flag = \DuckPhp\App::RunQuickly($options);
if($flag){
    return;
}
//*/
////]]]]


$application = $container->get(Application::class);

$request = $container->get(ServerRequestFactory::class)->createFromGlobals();
$request = $request->withAttribute('applicationStartTime', $startTime);

try {
    $application->start();
    $response = $application->handle($request);
    $emitter = new SapiEmitter();
    $emitter->emit($response, $request->getMethod() === Method::HEAD);
} finally {
    $application->afterEmit($response);
    $application->shutdown();
}
