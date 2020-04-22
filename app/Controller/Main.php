<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Controller;

use MY\Base\Helper\ControllerHelper as C;
use MY\Service\TestService;

class Main
{
    public function __construct()
    {
        $this->auth();
        C::setViewWrapper('layout/head','layout/foot');
    }
    protected function auth()
    {
        if(! C::GET('t')){
            C::Exit404();
            return;
        }
    }
    public function index()
    {
        C::Show(get_defined_vars(),'site/index');
    }
    public function contact()
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
    public function login()
    {
        $body = C::SG()->_POST;
        $error = null;
        
        if (!empty($body)) {
            try {
                UserService::G()->signup($body);
                C::ExitRedirect('/');
                return;
            } catch (\Throwable $e) {
                C::Logger()->error($e);
                $error = $e->getMessage();
            }
        }
        $data=[
            'body' => $body,
            'error' => $error,
            'csrf' => $request->getAttribute('csrf_token'),
        ];
        
        C::Show($data,'site/login');
    }
    public function logout()
    {
        UserService::G()->logout();
        C::ExitRedirect('/');
    }
    public function signup()
    {
        $body = C::SG()->_POST;
        $error = null;
        
        if (!empty($body)) {
            try {
                UserService::G()->signup($body);
                C::ExitRedirect('/');
                return;
            } catch (\Throwable $e) {
                C::Logger()->error($e);
                $error = $e->getMessage();
            }
        }
        $data=[
            'body' => $body,
            'error' => $error,
            'csrf' => $request->getAttribute('csrf_token'),
        ];
        
        C::Show($data,'site/signup');
    }
    public function test()
    {
        C::Show(get_defined_vars(),'site/index');
    }

}
