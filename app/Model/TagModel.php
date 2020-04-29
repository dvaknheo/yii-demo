<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Model;

use MY\Base\BaseModel;
use MY\Base\Helper\ModelHelper as M;

class TagModel extends BaseModel
{
    public function foo()
    {
        return DATE(DATE_ATOM);
    }
    public static function getTags()
    {
        $sql="SELECT `label`, count(*) `count`
FROM `tag` AS `tag` 
INNER JOIN `post_tag` AS `tag_posts_pivot`
    ON `tag_posts_pivot`.`tag_id` = `tag`.`id` 
INNER JOIN `post` AS `tag_posts`
    ON `tag_posts`.`id` = `tag_posts_pivot`.`post_id` AND `tag_posts`.`public` = TRUE  
GROUP BY `tag_posts_pivot`.`tag_id` 
ORDER BY `count` DESC
LIMIT 10";
        return M::DB()->fetchAll($sql);
    }
    public static function getTagByLabel($label)
    {
        $sql="select * from tag where label=?";
        $item=M::DB()->fetch($sql,$label);
        return $item;
    }
    public static function getTagsByPostId($id)
    {
        $sql="SELECT *
FROM `tag` AS `post_tags` 
INNER JOIN `post_tag` AS `l_post_tags_pivot`
    ON `l_post_tags_pivot`.`tag_id` = `post_tags`.`id`  
WHERE `l_post_tags_pivot`.`post_id` IN (?)";
        $tags=M::DB()->fetchAll($sql,$id);
        return $tags;
    }
    public static function getOrCreateTag($word)
    {
        $sql="select id from tag where label=?";
        $id=M::DB()->fetchColumn($sql,$word);
        if(!empty($id)){
            return $id;
        }
        $created_at = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $sql="insert into tag (`label`, `created_at`) VALUES (?,?)";
        M::DB()->execute($sql,$word,$created_at);
        
        return M::DB()->lastInsertId();
    }
}
