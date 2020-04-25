<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Service;

use MY\Base\BaseService;
use MY\Base\ServiceHelper;
use MY\Base\App;

class SessionService extends BaseService
{
    // 注意这里是有状态的，和其他 Service 不同。
    // 属于特殊的 Service
    public function __construct()
    {
        //session_start();
        App::session_start();
    }
    public function getCurrentUser()
    {
        $user = isset($_SESSION['user'])?$_SESSION['user']:[];
        
        return $user;
    }
    public function getCurrentUid()
    {
        $user = isset($_SESSION['user'])?$_SESSION['user']:[];
        
        return $user['id'];
    }
    public function setCurrentUser($user)
    {
        $_SESSION['user'] = $user;
    }
    public function logout()
    {
        //unset($_SESSION);
        App::session_destroy();
    }
    public function adminLogin()
    {
        $_SESSION['admin_logined'] = true;
    }
    public function checkAdminLogin()
    {
        return isset($_SESSION['admin_logined'])?true:false;
    }
    public function adminLogout()
    {
        unset($_SESSION['admin_logined']);
    }

    public function csrf_check($token)
    {
        return isset($_SESSION['_CSRF']) && $_SESSION['_CSRF'] === $token?true:false;
    }
    
    ////////////////////////////////////////////////////////////////////////
    public function csrf_token()
    {
        if(!isset(App::SG()->_SESSION['_token'])){
            $token=$this->randomString(40);
            App::SG()->_SESSION['_token']=$token;
        }
        return App::SG()->_SESSION['_token'];
    }
    public function csrf_field()
    {
        return '<input type="hidden" name="_token" value="'.$this->csrf_token().'">';
    }
    protected function randomString($length = 16)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }


}
