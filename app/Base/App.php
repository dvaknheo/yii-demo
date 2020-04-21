<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Base;

use DuckPhp\App as DuckPhp_App;
use DuckPhp\Core\View;
use DuckPhp\Core\Route;


use MY\Base\Helper\ControllerHelper as C;

use MY\Controller\blog;

class App extends DuckPhp_App
{
    public $container;
    public $promise;
    
    public function onInit()
    {
        Route::G(BaseRoute::G());
        
        $controller = blog::class;

        $this->options['route_map']=[
            '@/api/user/{login}' => "!api@index",
            /*
            
            '/blog'                                                             =>"$controller@index",
            '~^blog(/(?<id>\d+))$'                                               =>"$controller@index",
            '~^blog/page/(?<slug>\w+)$'                                          =>"$controller@post",
            '~^blog/tag/(?<label>\w+)(/page(?<page>\d+))?$'                       =>"$controller@tag",
            '/blog/archive'                                                     =>"$controller@archive",
            '^blog/archive/(?<year>\d+)$'                                       =>"$controller@archive_yearly",
            '~^blog/archive/(?<year>\d+)-(?<month>\d+)(/page(?<page>\d+))?$'     =>"$controller@archive_monthly",
            //*/
        ];
        // ! => 'namespace\controller. z'
        
        //inhert.
        $this->container=$this->options['container']??null;
        $this->promise=$this->options['promise']??null;
        
        $ret = parent::onInit();
        
        return $ret;
    }
    protected function onRun()
    {
        // your code here
        return parent::onRun();
    }
}
