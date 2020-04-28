<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Controller;

use MY\Base\Helper\ControllerHelper as C;
use MY\Service\UserService;
use MY\Service\SessionService;

class Main
{
    public function __construct()
    {
        C::setViewWrapper('layout/head','layout/foot');
    }
    public function index()
    {
        C::Show(get_defined_vars(),'site/index');
    }
    public function contact()
    {
        $body = C::SG()->_POST;
        $parameters = [
            'body' => $body,
        ];
        if (!empty($body)) {
            $sent = false;
            $error = '';
            try {
                $file = C::getUploadFile('file');
                UserService::G()->sendMail($body, $file);
                $sent = true;
            } catch (\Throwable $e) {
                $this->logger->error($e);
                $error = $e->getMessage();
            }
            $parameters['sent'] = $sent;
            $parameters['error'] = $error;
        }
        $parameters['csrf'] = SessionService::G()->csrf_token();
        C::Show($parameters,'contact/form');
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
            'csrf' => SessionService::G()->csrf_token(),
        ];
        
        C::Show($data,'auth/login');
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
            'csrf' => SessionService::G()->csrf_token(),
        ];
        
        C::Show($data,'signup/signup');
    }
    public function test1()
    {
        $x=UserService::G()->create('b00001','123456');
        var_dump($x);
        var_dump(date(DATE_ATOM));
        //C::Show(get_defined_vars(),'site/index');
    }
    public function test2()
    {
        //
    }

}
