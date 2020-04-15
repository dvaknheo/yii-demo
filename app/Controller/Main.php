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
    public function test()
    {
        C::Show(get_defined_vars(),'site/index');
    }
}
