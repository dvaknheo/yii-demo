<?php

namespace App\Controller;

use App\Service\UserService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Yii\Web\Data\DataResponseFactoryInterface;

class ApiUserController
{
    private DataResponseFactoryInterface $responseFactory;

    public function __construct(DataResponseFactoryInterface $responseFactory,ContainerInterface $container)
    {
        $this->responseFactory = $responseFactory;
        
        UserService::G()->setContainer($container);
    }

    public function index(): ResponseInterface
    {
        $items = UserService::G()->all();
        return $this->responseFactory->createResponse($items);
    }

    public function profile(Request $request): ResponseInterface
    {
        $login = $request->getAttribute('login', null);
        $data = UserService::G()->simpleProfile($login);
        
        if (empty($data)) {
            return $this->responseFactory->createResponse('Page not found', 404);
        }

        return $this->responseFactory->createResponse($data);
    }
}
