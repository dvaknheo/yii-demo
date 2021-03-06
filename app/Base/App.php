<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Base;

use DuckPhp\App as DuckPhp_App;
use DuckPhp\Core\Route;

use MY\Base\Helper\ControllerHelper as C;

class App extends DuckPhp_App
{
    protected $isExited=false;
    
    public function __construct()
    {
        require_once(__DIR__.'/functions.php');
        parent::__construct();
        
        //$this->options['skip_exception_check'] = true;   the default exception view no show error file ...
        $this->options['use_short_functions'] = true; 
        
        $this->options['skip_404_handler'] = true;
        $this->options['is_debug'] = true;
        $this->options['error_404']=function(){};
        
        $this->options['path_config'] = realpath(__DIR__.'/../config');
        $this->options['path_view'] = realpath(__DIR__.'/../view');
        
    }
    public function onInit()
    {
        static::Pager(BasePager::G());
        $ret = parent::onInit();
        
        $routes=static::LoadConfig('routes');
        
        // fixe ext routemap rewrite
        $namespace_prefix=$this->options['namespace'].'\\Controller\\';
        foreach($routes as &$v){
            $v=str_replace('#',$namespace_prefix,$v);
        }
        unset($v);
        
        static::assignImportantRoute($routes);
        
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
    protected function curl_file_get_contents($url)
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
    
    public function _ExitJson($ret, $exit = true)
    {
        static::header('Content-Type:application/json');
        $flag = JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK;
        echo json_encode($ret, $flag);
    }
    public function _H(&$str)
    {
        // for compatable;
        
        $doubleEncode=true;
        return htmlspecialchars(
        $str,
        ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5,
        ini_get('default_charset'),
        $doubleEncode
    );
    }
}
