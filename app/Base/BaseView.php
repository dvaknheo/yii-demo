<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Base;

use DuckPhp\Core\View;

class BaseView extends View
{
    public function __construct()
    {
        parent::__construct();
        $this->options['path_view']='view';
    }
    public function _Show($data = [], $view)
    {
        return parent::_Show($data, $view);
    }
}
