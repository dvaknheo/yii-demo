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
        C::setViewWrapper('layout/head','layout/foot');
    }
    protected function getAttribute($key,$default)
    {
        $data = C::getParameters();
        return $data[$key]??$default;
    }
    public function index()
    {
        $pageNum=(int)$this->getAttribute('page',1);

        list($total,$data) = UserService::G()->listByPage($pageNum);
        
        C::PageExt('/user/page-{page}',$pageNum);
        $pagehtml=C::PageHtml($total);
        $p=[
            'data'=>$data,
            'total'=>$total,
            'pager'=>$pagehtml,
        ];
        C::Show($p,'user/index');
    }
    public function profile()
    {
        $login = $this->getAttribute('login', null);
        $item =  UserService::G()->profile($login);
        if ($item === null) {
            C::Exit404();
            return;
        }
        $item['created_at']=date('h:i:s d.m.Y',strtotime($item['created_at']));
        C::Show(['item' => $item],'user/profile');
    }
}