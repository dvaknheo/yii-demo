<?php

namespace App\Controller;

use App\Controller;
use App\Parameters;
use App\Service\UserService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Http\Method;
use Yiisoft\Mailer\MailerInterface;
use Yiisoft\View\WebView;
use Yiisoft\Yii\Web\Data\DataResponseFactoryInterface;

class ContactController extends Controller
{
    private MailerInterface $mailer;
    private LoggerInterface $logger;
    private Parameters $parameters;

    public function __construct(
        DataResponseFactoryInterface $responseFactory,
        Aliases $aliases,
        WebView $view,
        User $user,
        MailerInterface $mailer,
        LoggerInterface $logger,
        Parameters $parameters,
        ContainerInterface $container
    ) {
        $this->mailer = $mailer;
        $this->logger = $logger;
        parent::__construct($responseFactory, $user, $aliases, $view, $container);
        $this->parameters = $parameters;
    }

    protected function getId(): string
    {
        return 'contact';
    }

    public function contact(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();
        $parameters = [
            'body' => $body,
        ];
        if ($request->getMethod() === Method::POST) {
            $sent = false;
            $error = '';

            try {
                $files = $request->getUploadedFiles();

                if (!empty($files['file']) && $files['file']->getError() === UPLOAD_ERR_OK) {
                    $file = $files['file'];
                }else{
                    $file=null;
                }
                $to = $this->parameters->get('supportEmail');
                $from = $this->parameters->get('mailer.username');
                UserService::G()->sendMail($body, $file,$to,$from);
                $sent = true;
            } catch (\Throwable $e) {
                $this->logger->error($e);
                $error = $e->getMessage();
            }
            $parameters['sent'] = $sent;
            $parameters['error'] = $error;
        }

        $parameters['csrf'] = $request->getAttribute('csrf_token');

        return $this->render('form', $parameters);
    }
}
