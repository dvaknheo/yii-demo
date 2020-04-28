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
        $sql_total=M::SqlForCountSimply($sql);
        $sql_page=M::SqlForPage($sql, $pageNum, $pageSize);
        $total=M::DB()->fetchColumn($sql_total);
        $data=M::DB()->fetchAll($sql_page);
        return [$data,$total];
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