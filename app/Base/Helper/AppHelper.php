<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Base\Helper;

use DuckPhp\Helper\ModelHelper as Helper;

class AppHelper extends Helper
{
    // override or add your code here

    public static function OBStart()
    {
        //ob_start([static::class,'OBContent']);
        ob_start();
        ob_implicit_flush(0);
    }
    public static function OBEnd($file)
    {
        $content=ob_get_contents();
        ob_end_flush();
        $file= preg_replace('/\.php$/', '', $file).'.txt';
        file_put_contents($file,$content);
    }
}
