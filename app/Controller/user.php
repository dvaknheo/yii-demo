<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Controller;

use MY\Base\Helper\ControllerHelper as C;
use MY\Service\UserService;

class user
{
    public function __construct()
    {
    }
    protected function getAttribute($key,$default)
    {
        $data = C::getParameters();
        return $data[$key]??$default;
    }
    public function index()
    {
        $pageNum = (int)$this->getAttribute('page', 1);
        
        $paginator = UserService::G()->listByPage($pageNum);
        
        C::Show(['paginator' => $paginator],'user/index');
    }
    public function profile()
    {
    var_dump("??");
        $login = $this->getAttribute('login', null);
        $item =  UserService::G()->profile($login);
        if ($item === null) {
            C::Exit404();
            return;
        }
        C::Show(['item' => $item],'user/profile');
    }
}