<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Base;

use DuckPhp\SingletonEx;

use MY\Base\Helper\ModelHelper as M;

// use DuckPhp\Base\StrictModelTrait;

class BaseModel
{
    use SingletonEx;
    // use StrictModelTrait;  // if you want to use strick check

    // override or add your code here
    protected static function listBySql($sql,$pageNum,$pageSize,...$args)
    {
        $sql_total=static::SqlForCountSimply($sql);
        $sql_page=static::SqlForPage($sql, $pageNum, $pageSize);
        $total=M::DB()->fetchColumn($sql_total,...$args);
        $list=M::DB()->fetchAll($sql_page,...$args);
        return [$total,$list];
    }
    // override or add your code here
    protected static function SqlForPage($sql, $pageNo, $pageSize)
    {
        $start = ($pageNo-1)*$pageSize;
        $sql.=" LIMIT $start,$pageSize";
        return $sql;
    }
    protected static function SqlForCountSimply($sql)
    {
        $sql=preg_replace_callback('/^\s*select(.*?)\sfrom\s/is',function($m){return 'SELECT COUNT(*) as c FROM ';},$sql);
        return $sql;
    }

}
