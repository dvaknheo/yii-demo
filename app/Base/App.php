<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Base;

use DuckPhp\App as DuckPhp_App;
use DuckPhp\Core\View;


use MY\Base\Helper\ControllerHelper as C;

use MY\Controller\blog;

class App extends DuckPhp_App
{
    public function onInit()
    {
        $controller = blog::class;

        $this->options['route_map']=[
            /*
            '/xblog'                                                             =>"$controller@index",
            '/xblog'                                                             =>"$controller@index",
            '~^blog(/(?<id>\d+))$'                                               =>"$controller@index",
            '~^blog/page/(?<slug>\w+)$'                                          =>"$controller@post",
            '~^blog/tag/(?<label>\w+)(/page(?<page>\d+))?$'                       =>"$controller@tag",
            '/blog/archive'                                                     =>"$controller@archive",
            '^blog/archive/(?<year>\d+)$'                                       =>"$controller@archive_yearly",
            '~^blog/archive/(?<year>\d+)-(?<month>\d+)(/page(?<page>\d+))?$'     =>"$controller@archive_monthly",
            //*/
        ];
        $ret = parent::onInit();
        BaseView::G()->init($this->options);
        View::G(BaseView::G());
        
        
        
        
        return $ret;
    }
    protected function onRun()
    {
        // your code here
        return parent::onRun();
    }
}
