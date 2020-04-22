<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Base;

use DuckPhp\Core\Route;

class BaseRoute extends Route
{
    //@override fixed parent bug
    public function run()
    {
        $flag = parent::run();
        return $flag && $this->getRunResult();
    }
}
