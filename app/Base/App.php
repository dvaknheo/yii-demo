<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Base;

use DuckPhp\App as DuckPhp_App;
use DuckPhp\Core\Route;

use MY\Base\Helper\ControllerHelper as C;

use MY\Controller\blog;

class App extends DuckPhp_App
{
    protected $isExited=false;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->options['skip_setting_file'] = true;
        $this->options['skip_404_handler'] = true;
        $this->options['is_debug'] = true;
    }
    public function onInit()
    {
        $this->options['path_config'] = basename($this->options['path']).'/config';
        //$this->options['path_view'] = basename($this->options['path']).'/view';
        //Route::G(BaseRoute::G());
        
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
    public function run():bool
    {
        $domain=static::Domain();
        if($domain==='yii3-init.demo.dev'){
            return false;
        }
        
        ob_start();
        ob_implicit_flush(0);
        $flag=parent::run();
        
        $data=ob_get_contents();
        ob_end_flush();
        
        if($this->isExited){
            return false;
        }
        //$this->diff($data);
        
        return $flag;
    }
    public function _OnDefaultException($ex):void
    {
        if($ex instanceof ExitException){
            $this->isExited = true;
            return;
        }
        parent::_OnDefaultException($ex);
    }
    public function _exit($code=0)
    {
        throw new ExitException();
    }
    protected function diff($data)
    {
        $url="http://yii3.demo.dev".$path;
        $data=$this->curl_file_get_contents([$url,'127.0.0.1:80']);
    }
    function curl_file_get_contents($url)
    {
        $ch = curl_init();
        
        if (is_array($url)) {
            list($base_url, $real_host) = $url;
            $url = $base_url;
            $host = parse_url($url, PHP_URL_HOST);
            $port = parse_url($url, PHP_URL_PORT);
            $c = $host.':'.$port.':'.$real_host;
            curl_setopt($ch, CURLOPT_CONNECT_TO, [$c]);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        
        
        $data = curl_exec($ch);
        curl_close($ch);
        return $data !== false?$data:'';
    }
}
