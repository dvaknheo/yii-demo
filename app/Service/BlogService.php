<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */

namespace MY\Service;

use MY\Base\BaseService;
use MY\Base\Helper\ServiceHelper as S;
use MY\Base\Helper\ModelHelper as M;
use MY\Model\PostModel;
use MY\Model\PostTagModel;
use MY\Model\TagModel;
use MY\Model\UserModel;

class BlogService extends BaseService
{
    private const POSTS_PER_PAGE = 3;
    private const POPULAR_TAGS_COUNT = 10;
    private const ARCHIVE_MONTHS_COUNT = 12;
    
    public function getDataToIndex($pageNum = 1,$pageSize=self::POSTS_PER_PAGE)
    {
        list($total,$list)=PostModel::getPosts($pageNum,$pageSize);
    
        $list=$this->addDataToPostCard($list);
        
        $data = [
            'total'=> $total,
            'list' => $list,
            'tags' => TagModel::getTags(),
            'archive' => PostModel::getArchivesCount(),
        ];
        return $data;
    }
    public function getPostData($slug)
    {
        $sql="SELECT *
FROM `post` AS `post` 
LEFT JOIN `user` AS `l_post_user`
    ON `l_post_user`.`id` = `post`.`user_id`  
WHERE `post`.`slug` = ? AND `post`.`public` = TRUE ";
        $item=M::DB()->fetch($sql,$label);
    $sql="SELECT *
FROM `tag` AS `post_tags` 
INNER JOIN `post_tag` AS `l_post_tags_pivot`
    ON `l_post_tags_pivot`.`tag_id` = `post_tags`.`id`  
WHERE `l_post_tags_pivot`.`post_id` IN (?)";
        $item=M::DB()->fetchAll($sql,$post['id']);
    $sql="SELECT *
FROM `comment` AS `post_comments` 
LEFT JOIN `user` AS `post_comments_user`
    ON `post_comments_user`.`id` = `post_comments`.`user_id`  
WHERE `post_comments`.`post_id` IN (?) AND `post_comments`.`public` = TRUE 
ORDER BY `post_comments`.`published_at` DESC";
        $item=M::DB()->fetchAll($sql,$post['id']);
        return $item;
    }
    public function getTagData($label, $pageNum)
    {
        $ret = [
            'item' => null,
        ];
        $item=TagModel::getTagByLabel($label);
        if(!$item){
            return $ret;
        }
        $ret['item']=$item;
        
        list($total,$list)=PostModel::getPostByTag($item['id'],$pageNum,$pageSize=3);
        foreach($list as &$v){
            $v['date_published_at']=date('H:i d.m.Y',strtotime($v['published_at']));
        }
        $ret['total']=$total;
        $ret['list']=$list;
        
        return $ret;
    }
    public function getArchiveData()
    {
        return PostModel::getArchiveData();
    }
    public function getArchiveDataMonthly($year,$month,$pageNum,$pageSize=5)
    {
        list($total,$list)=PostModel::getArchiveDataMonthly($year,$month,$pageNum,$pageSize);
        $list=$this->addDataToPostCard($list);
        return [$total,$list];
    }
    protected function addDataToPostCard($list)
    {
        $users=UserModel::getUserByIdList(array_column($list,'user_id'));
        $tags=PostTagModel::getTagsByIdList(array_column($list,'id'));
        
        foreach($list as &$v){
            $v['user']=$users[$v['user_id']]??[];
            $v['tags']=$tags[$v['id']]??[];
            $v['content_short']=mb_substr($v['content'], 0, 400). (mb_strlen($v['content']) > 400 ? '…' : '');
            $v['month_published_at']=date('M, d',strtotime($v['published_at']));
        }
        return $list;
    }
    public function getArchiveDataYearly($year)
    {
        $ret=PostModel::getArchiveDataYearly($year);
        /*
        foreach($ret as &$v){
            $ret[$k]['month'] = date('m',strtotime($v['published_at']));
            $ret[$k]['monthName'] = \DateTime::createFromFormat('!m', $ret[$k]['month'])->format('F');
        }
        */
        return $ret;
    }
}
