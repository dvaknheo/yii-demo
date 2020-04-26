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
        C::header('Content-Type:application/xml; UTF-8');
        echo <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<response><status>success</status><data><version>1.0</version><author>yiisoft</author></data></response>

EOT;
    }
    public function v2()
    {
        // OK, the lazyest ^_^
        C::header('Content-Type:application/json');
        echo '{"status":"success","data":{"version":"2.0","author":"yiisoft"}}';
    }
}
