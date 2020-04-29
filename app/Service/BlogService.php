<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */

namespace MY\Service;

use MY\Base\BaseService;
use MY\Base\Helper\ServiceHelper as S;
use MY\Base\Helper\ModelHelper as M;

class BlogService extends BaseService
{
    private const POSTS_PER_PAGE = 3;
    private const POPULAR_TAGS_COUNT = 10;
    private const ARCHIVE_MONTHS_COUNT = 12;
    
    public function getDataToIndex($pageNum = 1)
    {
        $postRepo = $this->getORM()->getRepository(Post::class);
        $dataReader = $postRepo->findAllPreloaded();
        $paginator = (new OffsetPaginator($dataReader))
            ->withPageSize(self::POSTS_PER_PAGE)
            ->withCurrentPage($pageNum);

        
        $data = [
            'paginator' => $paginator,
            'tags' => $this->getTags(),
            'archive' => $this->getArchives(),
        ];
        return $data;
    }
    private function getData()
    {
/*
2020-04-28 14:39:29.553000 [info][application] SELECT COUNT(DISTINCT(`post`.`id`))
FROM `post` AS `post`
WHERE `post`.`public` = TRUE 
2020-04-28 14:39:29.587200 [info][application] SELECT `post`.`id` AS `c0`, `post`.`slug` AS `c1`, `post`.`title` AS `c2`, `post`.`public` AS `c3`, `post`.`content` AS `c4`, `post`.`created_at` AS `c5`, `post`.`updated_at` AS `c6`,
`post`.`published_at` AS `c7`, `post`.`deleted_at` AS `c8`, `post`.`user_id` AS `c9`
FROM `post` AS `post`
WHERE `post`.`public` = TRUE 
ORDER BY `post`.`published_at` DESC
LIMIT 3 OFFSET 0
2020-04-28 14:39:29.587600 [info][application] SELECT `post_user`.`id` AS `c0`, `post_user`.`token` AS `c1`, `post_user`.`login` AS `c2`, `post_user`.`password_hash` AS `c3`, `post_user`.`created_at` AS `c4`, `post_user`.`updated_at`
AS `c5`
FROM `user` AS `post_user`
WHERE `post_user`.`id` IN (5 ,31 ,29) 
2020-04-28 14:39:29.589100 [info][application] SELECT `l_post_tags_pivot`.`id` AS `c0`, `l_post_tags_pivot`.`post_id` AS `c1`, `l_post_tags_pivot`.`tag_id` AS `c2`, `post_tags`.`id` AS `c3`, `post_tags`.`label` AS `c4`,
`post_tags`.`created_at` AS `c5`
FROM `tag` AS `post_tags` 
INNER JOIN `post_tag` AS `l_post_tags_pivot`
    ON `l_post_tags_pivot`.`tag_id` = `post_tags`.`id`  
WHERE `l_post_tags_pivot`.`post_id` IN (2 ,20 ,18) 
//*/
    }
    private function getTags()
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
    private function getArchives()
    {
    $sql="SELECT count(`post`.`id`) `count`, extract(month from post.published_at) month, extract(year from post.published_at) year
FROM `post` AS `post`
WHERE `post`.`public` = TRUE 
GROUP BY `year`, `month` 
ORDER BY `year` DESC, `month` DESC
LIMIT 12";
        return M::DB()->fetchAll($sql);
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
        $sql="select * from tag where label=?";
        $item=M::DB()->fetch($sql,$label);
        if(!$item){
            return $ret;
        }
        $ret['item']=$item;
        $sql="SELECT *
FROM `post` AS `post` 
INNER JOIN `post_tag` AS `post_tags_pivot`
    ON `post_tags_pivot`.`post_id` = `post`.`id` 
INNER JOIN `tag` AS `post_tags`
    ON `post_tags`.`id` = `post_tags_pivot`.`tag_id` 
LEFT JOIN `user` AS `l_post_user`
    ON `l_post_user`.`id` = `post`.`user_id`  
WHERE `post_tags`.`id` = ? AND `post`.`public` = TRUE ";
        list($total,$list)=M::QuicklyGetPageData($sql,$pageNum,3,$item['id']);
        foreach($list as &$v){
            $v['date_published_at']=date('H:i d.m.Y',strtotime($v['published_at']));
        }
        $ret['total']=$total;
        $ret['list']=$list;
        
        return $ret;
    }
    public function getArchiveData()
    {
        $sql="SELECT count(`post`.`id`) `count`, extract(month from post.published_at) month, extract(year from post.published_at) year
FROM `post` AS `post`
WHERE `post`.`public` = TRUE 
GROUP BY `year`, `month` 
ORDER BY `year` DESC, `month` DESC";
        $data=M::DB()->fetchAll($sql);
        return $data;
    }
    public function getArchiveDataMonthly($year,$month,$pageNum,$pageSize=5)
    {
        $begin = (new \DateTimeImmutable())->setDate($year, $month, 1)->setTime(0, 0, 0);
        $end = $begin->setDate($year, $month + 1, 1)->setTime(0, 0, -1);
        $begin=$begin->format('Y-m-d H:i:s O');
        $end=$end->format('Y-m-d H:i:s O');
        
        $sql="SELECT *
FROM `post` AS `post`
WHERE `post`.`published_at` BETWEEN ? AND ? AND `post`.`public` = TRUE";
        $sql_total=M::SqlForCountSimply($sql,$begin,$end);
        $sql_page=M::SqlForPage($sql, $pageNum, $pageSize);
        $total=M::DB()->fetchColumn($sql_total,$begin,$end);
        $list=M::DB()->fetchAll($sql_page,$begin,$end);
        
        if(empty($list)){
            return [0,[]];
        }
        $sql="SELECT *
FROM `user` AS `post_user`
WHERE `post_user`.`id` IN (%s)";
        $ids=implode(',',array_column($list,'user_id'));
        
        $data=M::DB()->fetchAll(sprintf($sql,$ids));
        foreach($data as $v){
            $users[$v['id']]=$v;
        }
        $sql="SELECT *
FROM `tag` AS `post_tags` 
INNER JOIN `post_tag` AS `l_post_tags_pivot`
    ON `l_post_tags_pivot`.`tag_id` = `post_tags`.`id`  
WHERE `l_post_tags_pivot`.`post_id` IN (%s)";
        $ids=implode(',',array_column($list,'id'));
        $data=M::DB()->fetchAll(sprintf($sql,$ids));
        
        $tags=[];
        foreach($data as $v){
            $tags[$v['post_id']]=$tags[$v['post_id']]??[];
            $tags[$v['post_id']][]=$v;
        }
        foreach($list as &$v){
            $v['user']=$users[$v['user_id']]??[];
            $v['tags']=$tags[$v['id']]??[];
            $v['content_short']=mb_substr($v['content'], 0, 400). (mb_strlen($v['content']) > 400 ? '…' : '');
            $v['month_published_at']=date('M, d',strtotime($v['published_at']));
        }
        
        
        return [$total,$list];
    }
    public function getArchiveDataYearly($year)
    {
        $begin = (new \DateTimeImmutable())->setDate($year, 1, 1)->setTime(0, 0, 0);
        $end = $begin->setDate($year + 1, 1, 1)->setTime(0, 0, -1);
        $begin=$begin->format('Y-m-d H:i:s O');
        $end=$end->format('Y-m-d H:i:s O');
        
        $sql="SELECT post.*,l_post_user.login
FROM `post` AS `post` 
LEFT JOIN `user` AS `l_post_user`
    ON `l_post_user`.`id` = `post`.`user_id`  
WHERE `post`.`published_at` BETWEEN ? AND ? AND `post`.`public` = TRUE 
ORDER BY `post`.`published_at` ASC";
        $data=M::DB()->fetchAll($sql,$begin,$end);
        
        $ret=[];
        foreach($data as $k=>$v){
            $ret[$k]['month'] = date('m',strtotime($v['published_at']));
            $ret[$k]['monthName'] = \DateTime::createFromFormat('!m', $ret[$k]['month'])->format('F');
            $ret[$k]['login'] = $v['login'];
            $ret[$k]['title'] = $v['title'];
            $ret[$k]['slug'] = $v['slug'];
        }
        return $ret;

    }
}
