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
    public function __construct()
    {
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
        return $user['id']??null;
    }
    public function setCurrentUser($user)
    {
        $_SESSION['user'] = $user;
    }
    public function logout()
    {
        App::SG()->_SESSION['user']=null;
        unset(App::SG()->_SESSION['user']);
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
