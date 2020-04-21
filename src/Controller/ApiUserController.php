<?php

namespace App\Controller;

use Cycle\ORM\ORMInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Yii\Web\Data\DataResponseFactoryInterface;

use MY\Service\UserService;

class ApiUserController
{
    private DataResponseFactoryInterface $responseFactory;

    public function __construct(DataResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
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
