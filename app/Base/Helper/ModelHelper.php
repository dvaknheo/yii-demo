<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Base\Helper;

use DuckPhp\Helper\ModelHelper as Helper;

class ModelHelper extends Helper
{
    // override or add your code here
    public static function SqlForPage($sql, $pageNo, $pageSize)
    {
        $start = ($pageNo-1)*$pageSize;
        $sql.=" LIMIT $start,$pageSize";
        return $sql;
    }
    public static function SqlForCountSimply($sql)
    {
        $sql=preg_replace_callback('/^\s*select(.*?)\sfrom\s/is',function($m){return 'SELECT COUNT(*) as c FROM ';},$sql);
        return $sql;
    }
    public static function QuicklyGetPageData($sql, $pageNo, $pageSize,...$args)
    {
        $total=static::DB()->fetchColumn(static::SqlForCountSimply($sql),...$args);
        $sql=static::SqlForPage($sql, $pageNo, $pageSize);
        $list=static::DB()->fetchAll($sql,...$args);
        return [$total,$list];
    }
}
