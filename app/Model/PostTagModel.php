<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Model;

use MY\Base\BaseModel;
use MY\Base\Helper\ModelHelper as M;

class PostTagModel extends BaseModel
{
    public function foo()
    {
        return DATE(DATE_ATOM);
    }
    public static function getTagsByIdList($id_list)
    {
        if(empty($id_list)){
            return [];
        }
$sql="SELECT *
FROM `tag` AS `post_tags` 
INNER JOIN `post_tag` AS `l_post_tags_pivot`
    ON `l_post_tags_pivot`.`tag_id` = `post_tags`.`id`  
WHERE `l_post_tags_pivot`.`post_id` IN (%s)";

        $ids=implode(',',$id_list);
        $data=M::DB()->fetchAll(sprintf($sql,$ids));
        
        $tags=[];
        foreach($data as $v){
            $tags[$v['post_id']]=$tags[$v['post_id']]??[];
            $tags[$v['post_id']][]=$v;
        }
        return $tags;
    }
}
