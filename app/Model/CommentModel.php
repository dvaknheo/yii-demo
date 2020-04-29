<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Model;

use MY\Base\BaseModel;
use MY\Base\Helper\ModelHelper as M;

class CommentModel extends BaseModel
{
    public function foo()
    {
        return DATE(DATE_ATOM);
    }
    public static function getCommentsByPostId($id)
    {
$sql="SELECT *
FROM `comment` AS `post_comments` 
LEFT JOIN `user` AS `post_comments_user`
    ON `post_comments_user`.`id` = `post_comments`.`user_id`  
WHERE `post_comments`.`post_id` IN (?) AND `post_comments`.`public` = TRUE 
ORDER BY `post_comments`.`published_at` DESC";
        return M::DB()->fetchAll($sql,$id);
    }
    public static function addComment($user_id,$post_id,$content)
    {
        $public = (rand(0, 3) > 0)?true:false;
        $published_at = $public?(new \DateTimeImmutable(date('r', rand(time(), strtotime('-1 years')))))->format('Y-m-d H:i:s'):null;
        $created_at = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $updated_at = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $data=[
            'public'=>$public,
            'content'=>$content,
            
            'created_at'=>$created_at,
            'updated_at'=>$updated_at,
            'published_at'=>$published_at,
            'public'=>$public,
            'deleted_at'=>null,
            'post_id'=>$post_id,
            
            'user_id'=>$user_id,
        ];
        
        M::DB()->insertData('comment',$data);
        
        return M::DB()->lastInsertId();
    }
}
