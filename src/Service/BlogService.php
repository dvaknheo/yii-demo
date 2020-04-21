<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */

namespace MY\Service;

use MY\Base\BaseService;
use MY\Base\Helper\ServiceHelper as S;

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
        return $this->getObject(ArchiveRepository::class)->getFullArchive();
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
        return  $this->getObject(ArchiveRepository::class)->getYearlyArchive($year);
    }
}
