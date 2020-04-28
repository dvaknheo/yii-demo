<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */

namespace MY\Service;

use MY\Base\BaseService;
use MY\Base\Helper\ServiceHelper as S;
use MY\Base\Helper\ModelHelper as M;

use App\Blog\Entity\Post;
use App\Blog\Entity\Tag;
use App\Blog\Archive\ArchiveRepository;

use Yiisoft\Data\Paginator\OffsetPaginator;

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

        $archive = $this->getObject(ArchiveRepository::class)->getFullArchive()->withLimit(self::ARCHIVE_MONTHS_COUNT);
        $tags = $this->getORM()->getRepository(Tag::class)->getTagMentions(self::POPULAR_TAGS_COUNT);
        
        $data = [
            'paginator' => $paginator,
            'archive' => $archive,
            'tags' => $tags,
        ];
        return $data;
    }
    public function getPostData($slug)
    {
        $postRepo = $this->getORM()->getRepository(Post::class);
        $item = $postRepo->fullPostPage($slug);
        
        return $item;
    }
    public function getTagData($label, $pageNum)
    {
        $data = [
            'item' => null,
            'paginator' => null,
        ];
        
        $item = $this->getORM()->getRepository(Tag::class)->findByLabel($label);
        if ($item === null) {
            return $data;
        }

        /** @var PostRepository $postRepo */
        $postRepo = $this->getORM()->getRepository(Post::class);
        // preloading of posts
        $paginator = (new OffsetPaginator($postRepo->findByTag($item->getId())))
            ->withPageSize(self::POSTS_PER_PAGE)
            ->withCurrentPage($pageNum);

        $data = [
            'item' => $item,
            'paginator' => $paginator,
        ];
        return $data;
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
    public function getArchiveDataMonthly($year,$month,$pageNum)
    {
        $archiveRepo=$this->getObject(ArchiveRepository::class);
        $dataReader = $archiveRepo->getMonthlyArchive($year, $month);
        
        $paginator = (new OffsetPaginator($dataReader))
            ->withPageSize(self::POSTS_PER_PAGE)
            ->withCurrentPage($pageNum);
            
        $archive = $archiveRepo->getFullArchive()->withLimit(12);
        $tags = $this->getORM()->getRepository(Tag::class)->getTagMentions(self::POPULAR_TAGS_COUNT);
        
        $data = [
            'year' => $year,
            'month' => $month,
            'paginator' => $paginator,
            'archive' => $archive,
            'tags' => $tags,
        ];
        return $data;
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
