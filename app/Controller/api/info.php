<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Controller\api;

use MY\Base\Helper\ControllerHelper as C;

class info
{
    public function __construct()
    {
    }
    public function v1()
    {
        $data=[
            'version'=>'1.0',
            'author'=>'yiisoft',
        ];
        C::MyExitXml($data);
    }
    public function v2()
    {
        $data=[
            'version'=>'2.0',
            'author'=>'yiisoft',
        ];
        C::MyExitJson($data);
        return;
    }
}
