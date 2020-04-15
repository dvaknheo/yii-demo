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
    public function getPostData()
    {
        $postRepo = $orm->getRepository(Post::class);
        $slug = $request->getAttribute('slug', null);

        $item = $postRepo->fullPostPage($slug);
        if ($item === null) {
            return $this->responseFactory->createResponse(404);
        }

        return $this->render('index', ['item' => $item]);
    }
    public function getTagData()
    {
        /** @var TagRepository $tagRepo */
        $tagRepo = $orm->getRepository(Tag::class);
        /** @var PostRepository $postRepo */
        $postRepo = $orm->getRepository(Post::class);
        $label = $request->getAttribute('label', null);
        $pageNum = (int)$request->getAttribute('page', 1);

        $item = $tagRepo->findByLabel($label);

        if ($item === null) {
            return $this->responseFactory->createResponse(404);
        }
        // preloading of posts
        $paginator = (new OffsetPaginator($postRepo->findByTag($item->getId())))
            ->withPageSize(self::POSTS_PER_PAGE)
            ->withCurrentPage($pageNum);

        $data = [
            'item' => $item,
            'paginator' => $paginator,
        ];
        return $this->render('index', $data);
    }
    public function getArchiveData()
    {
                return $this->render('index', ['archive' => $archiveRepo->getFullArchive()]);

    }
    public function getArchiveDataMonthly()
    {
        /** @var TagRepository $postRepo */
        $tagRepo = $orm->getRepository(Tag::class);

        $pageNum = (int)$request->getAttribute('page', 1);
        $year = $request->getAttribute('year', null);
        $month = $request->getAttribute('month', null);

        $dataReader = $archiveRepo->getMonthlyArchive($year, $month);
        $paginator = (new OffsetPaginator($dataReader))
            ->withPageSize(self::POSTS_PER_PAGE)
            ->withCurrentPage($pageNum);
        $data = [
            'year' => $year,
            'month' => $month,
            'paginator' => $paginator,
            'archive' => $archiveRepo->getFullArchive()->withLimit(12),
            'tags' => $tagRepo->getTagMentions(self::POPULAR_TAGS_COUNT),
        ];
    }
    public function getArchiveDataYearly($year)
    {
        $year = $request->getAttribute('year', null);

        $data = [
            'year' => $year,
            'items' => $archiveRepo->getYearlyArchive($year),
        ];
        return $data;
    }
}
