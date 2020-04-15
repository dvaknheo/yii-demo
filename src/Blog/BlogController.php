<?php

declare(strict_types=1);

namespace App\Blog;

use App\Blog\Archive\ArchiveRepository;
use App\Controller;
use App\Blog\Entity\Post;
use App\Blog\Entity\Tag;
use App\Blog\Post\PostRepository;
use App\Blog\Tag\TagRepository;
use Cycle\ORM\ORMInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Data\Paginator\OffsetPaginator;

use MY\Service\BlogService;


final class BlogController extends Controller
{
    private const POSTS_PER_PAGE = 3;
    private const POPULAR_TAGS_COUNT = 10;
    private const ARCHIVE_MONTHS_COUNT = 12;

    protected function getId(): string
    {
        return 'blog';
    }
    public function index(Request $request, ORMInterface $orm, ArchiveRepository $archiveRepo): Response
    {
        $pageNum = (int)$request->getAttribute('page', 1);
        $data = BlogService::G()->getDataToIndex($pageNum);
        
        return $this->render('index', $data);
    }
}
