<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Model;

use MY\Base\BaseModel;
use MY\Base\Helper\ModelHelper as M;

class UserModel extends BaseModel
{
    public static function findByLogin($login)
    {
        $sql="select * from user where login=?";
        return M::DB()->fetch($sql,$login);
    }
}
