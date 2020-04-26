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
        require_once(__DIR__.'/functions.php');
        parent::__construct();
        
        //$this->options['skip_exception_check'] = true; 
        $this->options['use_short_functions'] = true; 
        
        $this->options['skip_404_handler'] = true;
        $this->options['is_debug'] = true;
        $this->options['error_404']=function(){};
    }
    public function onInit()
    {
        $this->options['path_config'] = 'app/config';
        static::Pager(BasePager::G());
        $controller = blog::class;

        $this->options['route_map_important']=[
            '~^user(/page-(?<page>\d+))?$'      => '#user->index',
            '~^user/(?<login>\w+)$'             => '#user->profile',
            //'/blog'                             =>"#blog@index",

            //'@/api/user/{login}' => "!api@index",
            /*
            
            '/blog'                                                             =>"#blog@index",
            '~^blog(/(?<id>\d+))?$'                                              =>"#blog@index",
            '~^blog/page/(?<slug>\w+)$'                                         =>"#blog@post",
            '~^blog/tag/(?<label>\w+)(/page(?<page>\d+))?$'                     =>"#blog@tag",
            '/blog/archive'                                                     =>"#blog@archive",
            '^blog/archive/(?<year>\d+)$'                                       =>"#blog@archive_yearly",
            '~^blog/archive/(?<year>\d+)-(?<month>\d+)(/page(?<page>\d+))?$'    =>"#blog@archive_monthly",
            //*/
        ];
        foreach($this->options['route_map_important'] as &$v){
            $v=str_replace('#','MY\\Controller\\',$v);
        }
        unset($v);
        
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
        if($domain==='http://yii3-init.demo.dev'){
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
        if($flag){
            $this->diff($data);
        }
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
    protected function diff($data1)
    {
        $path=$_SERVER['REQUEST_URI'];
        $url="http://yii3-init.demo.dev".$path;
        $data2=$this->curl_file_get_contents([$url,'127.0.0.1:80']);
        
        $data1=$this->dealData($data1);
        $data2=$this->dealData($data2);
        
        if($data2===$data1){
            var_dump('same.');
            return true;
        }
        file_put_contents(__DIR__.'/../../runtime/a.log',$data1);
        file_put_contents(__DIR__.'/../../runtime/b.log',$data2);
        
        var_dump('different!');
        return false;
    }
    protected function dealData($data)
    {
        $p=preg_quote('name="_csrf" value="');
        $data=preg_replace('/'.$p.'[^"]+"/','name="_csrf" value=""',$data);
        return $data;
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
