<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Controller;

use MY\Base\Helper\ControllerHelper as C;
use MY\Service\UserService;

class api
{
    public function __construct()
    {
    }
    
    protected function getAttribute($key,$default)
    {
        $data = C::getParameters();
        return $data[$key]??$default;
    }
    public function user()
    {
        $items = UserService::G()->all();
        return C::MyExitXml($items);
    }
    public function profile()
    {
        $login = $this->getAttribute('login', null);

        $data = UserService::G()->simpleProfile($login);
        if (empty($data)) {
            C::Exit404();
            return;
        }
        C::MyExitJson($data);
    }
}
