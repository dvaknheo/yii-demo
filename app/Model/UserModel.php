<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Model;

use MY\Base\BaseModel;
use MY\Base\Helper\ModelHelper as M;

use Yiisoft\Security\Random;

class UserModel extends BaseModel
{
    public static function findByLogin($login)
    {
        $sql="select * from user where login=?";
        return M::DB()->fetch($sql,$login);
    }
    public static function listByPage($pageNum,$pageSize=30)
    {
        $sql="select * from user where true order by login ";
        return static::listBySql($sql,$pageNum,$pageSize);
    }
    public static function listAllSimple()
    {
        $sql="SELECT login,created_at from user where true order by login asc";
        $data=M::DB()->fetchAll($sql);
        return $data;
    }
    ////
    public static function create($login,$password)
    {
        $date= date('Y-m-d H:i:s');// new \DateTimeImmutable();
        $hash=password_hash($password,PASSWORD_BCRYPT,['cost'=>13]); // do not complex;
        $token=Random::string(128);
        $data=[
            'login'=>$login,
            'token'=>$token,
            'password_hash'=>$hash,
            'created_at'=>$date,
            'updated_at'=>$date,
        ];
        M::DB()->insertData('user',$data);
        return M::DB()->lastInsertId();
    }
    ///////////////
    public static function getUserByIdList($id_list)
    {
        if(empty($id_list)){
            return [];
        }
        $sql="SELECT * FROM `user` AS `post_user` WHERE `post_user`.`id` IN (%s)";
        $ids=implode(',',$id_list);
        $data=M::DB()->fetchAll(sprintf($sql,$ids));
        foreach($data as $v){
            $users[$v['id']]=$v;
        }
        return $users;
    }
    
    
}
/*

    public function validate(string $password, string $hash): bool
    {
        if ($password === '') {
            throw new \InvalidArgumentException('Password must be a string and cannot be empty.');
        }

        return password_verify($password, $hash);
    }
*/