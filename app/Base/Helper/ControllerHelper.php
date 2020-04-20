<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Base\Helper;

use DuckPhp\Core\Route;
use DuckPhp\Helper\ControllerHelper as Helper;

class ControllerHelper extends Helper
{
    public static function Exit404($flag = true)
    {
        Route::G()->forceFail();
        //return parent::Exit404($flag);
    }
}
