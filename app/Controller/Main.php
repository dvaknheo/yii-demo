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
        $this->auth();
        C::Show(get_defined_vars(),'site/index');
    }
    public function contact()
    {
        $this->auth();
        C::Show(get_defined_vars(),'site/contact');
    }
    public function login()
    {
        $this->auth();
        C::Show(get_defined_vars(),'site/login');
    }
    public function logout()
    {
        $this->auth();
        C::Show(get_defined_vars(),'site/logout');
    }
    public function signup()
    {
        $this->auth();
        C::Show(get_defined_vars(),'site/signup');
    }
    
    public function test()
    {
        C::Show(get_defined_vars(),'site/index');
    }

}
