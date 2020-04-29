<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Model;

use MY\Base\BaseModel;
use MY\Base\Helper\ModelHelper as M;

class PostModel extends BaseModel
{
    public static function get()
    {
    }
    public static function getPosts($pageNum,$pageSize=5)
    {
        $sql="SELECT *
FROM `post` AS `post`
WHERE `post`.`public` = TRUE ORDER BY `post`.`published_at` DESC
";
        $data=parent::listBySql($sql,$pageNum,$pageSize);
        return $data;
    }

    public static function getArchiveDataMonthly($year,$month,$pageNum,$pageSize=5)
    {
        $begin = (new \DateTimeImmutable())->setDate($year, $month, 1)->setTime(0, 0, 0);
        $end = $begin->setDate($year, $month + 1, 1)->setTime(0, 0, -1);
        $begin=$begin->format('Y-m-d H:i:s O');
        $end=$end->format('Y-m-d H:i:s O');
        
        $sql="SELECT *
FROM `post` AS `post`
WHERE `post`.`published_at` BETWEEN ? AND ? AND `post`.`public` = TRUE
ORDER BY `post`.`published_at` DESC";

        $data=parent::listBySql($sql,$pageNum,$pageSize,$begin,$end);
        return $data;
    }
    //@with user
    public static function getArchiveDataYearly($year)
    {
        $begin = (new \DateTimeImmutable())->setDate($year, 1, 1)->setTime(0, 0, 0);
        $end = $begin->setDate($year + 1, 1, 1)->setTime(0, 0, -1);
        $begin=$begin->format('Y-m-d H:i:s O');
        $end=$end->format('Y-m-d H:i:s O');
        
        $sql="SELECT post.title, post.slug, post.published_at , l_post_user.login
FROM `post` AS `post` 
LEFT JOIN `user` AS `l_post_user`
    ON `l_post_user`.`id` = `post`.`user_id`  
WHERE `post`.`published_at` BETWEEN ? AND ? AND `post`.`public` = TRUE 
ORDER BY `post`.`published_at` ASC";
        $data=M::DB()->fetchAll($sql,$begin,$end);

        foreach($data as $k=>$v){
        
            $ret[$k]['month'] = date('m',strtotime($v['published_at']));
            $ret[$k]['monthName'] = \DateTime::createFromFormat('!m', $ret[$k]['month'])->format('F');
            
            $ret[$k]['login'] = $v['login'];
            $ret[$k]['title'] = $v['title'];
            $ret[$k]['slug'] = $v['slug'];
        }
        return $ret;
    }
    public static function getArchiveData()
    {
        $sql="SELECT count(`post`.`id`) `count`, extract(month from post.published_at) month, extract(year from post.published_at) year
FROM `post` AS `post`
WHERE `post`.`public` = TRUE 
GROUP BY `year`, `month` 
ORDER BY `year` DESC, `month` DESC";
        $data=M::DB()->fetchAll($sql);
        return $data;
    }
    public static function getArchivesCount()
    {
    $sql="SELECT count(`post`.`id`) `count`, extract(month from post.published_at) month, extract(year from post.published_at) year
FROM `post` AS `post`
WHERE `post`.`public` = TRUE 
GROUP BY `year`, `month` 
ORDER BY `year` DESC, `month` DESC
LIMIT 12";
        return M::DB()->fetchAll($sql);
    }
    public static function getPostByTag($tagId,$pageNum=1,$pageSize=3)
    {
            $sql="SELECT *
FROM `post` AS `post` 
INNER JOIN `post_tag` AS `post_tags_pivot`
    ON `post_tags_pivot`.`post_id` = `post`.`id` 
INNER JOIN `tag` AS `post_tags`
    ON `post_tags`.`id` = `post_tags_pivot`.`tag_id` 
LEFT JOIN `user` AS `l_post_user`
    ON `l_post_user`.`id` = `post`.`user_id`  
WHERE `post_tags`.`id` = ? AND `post`.`public` = TRUE ";
        list($total,$list)=parent::listBySql($sql,$pageNum,$pageSize,$tagId);
    }
}
