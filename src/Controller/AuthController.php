<?php

namespace App\Controller;

use App\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Http\Method;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\Web\Data\DataResponseFactoryInterface;

use MY\Service\UserService;

class AuthController extends Controller
{
    private LoggerInterface $logger;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        DataResponseFactoryInterface $responseFactory,
        Aliases $aliases,
        WebView $view,
        User $user,
        LoggerInterface $logger,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->logger = $logger;
        $this->urlGenerator = $urlGenerator;
        parent::__construct($responseFactory, $user, $aliases, $view);
    }

    protected function getId(): string
    {
        return 'auth';
    }

    public function login(ServerRequestInterface $request): ResponseInterface 
    {
        $body = $request->getParsedBody();
        $error = null;

        if ($request->getMethod() === Method::POST) {
            try {
                UserService::G()->login($body);
                
                return $this->responseFactory
                    ->createResponse(302)
                    ->withHeader(
                        'Location',
                        $this->urlGenerator->generate('site/index')
                    );
            } catch (\Throwable $e) {
                $this->logger->error($e);
                $error = $e->getMessage();
            }
        }

        return $this->render(
            'login',
            [
                'csrf' => $request->getAttribute('csrf_token'),
                'body' => $body,
                'error' => $error,
            ]
        );
    }

    public function logout(): ResponseInterface
    {
        UserService::G()->logout();
        
        return $this->responseFactory
            ->createResponse(302)
            ->withHeader(
                'Location',
                $this->urlGenerator->generate('site/index')
            );
    }
}
