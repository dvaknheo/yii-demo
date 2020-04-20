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

$path = realpath(__DIR__.'/..');
$namespace = rtrim('MY\\', '\\');                    // @DUCKPHP_NAMESPACE
$options=[];
$options['path'] = $path;
$options['namespace'] = $namespace;

//$options['error_500'] = '_sys/error_500';
//$options['error_debug'] = '_sys/error_debug';

$options['skip_setting_file'] = true;
$options['skip_404_handler'] = true;
$options['skip_exception_check'] = true;
//$options['handle_all_exception'] = false;

$options['is_debug'] = true;                  // @DUCKPHP_DELETE
$options['container'] = $container;

\DuckPhp\App::G()->init($options);

$flag=\DuckPhp\App::G()->run();
if($flag){
    return;
}



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
