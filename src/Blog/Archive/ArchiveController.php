<?php

namespace App\Blog\Archive;

use App\Controller;
use MY\Service\BlogService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ArchiveController extends Controller
{
    private const POSTS_PER_PAGE = 3;
    private const POPULAR_TAGS_COUNT = 10;

    protected function getId(): string
    {
        return 'blog/archive';
    }

    public function index(): Response
    {
        $archive = BlogService::G()->getArchiveData();
        return $this->render('index', ['archive' => $archive]);
    }

    public function monthlyArchive(): Response
    {
        $pageNum = (int)$request->getAttribute('page', 1);
        $year = (int)$request->getAttribute('year', null);
        $month = (int)$request->getAttribute('month', null);
        
        $data = BlogService::G()->getArchiveDataMonthly($year,$month,$pageNum);
        
        return $this->render('monthly-archive', $data);
    }

    public function yearlyArchive(): Response
    {
        $year = $request->getAttribute('year', null);
        $items = BlogService::G()->getArchiveDataYearly((int)$year);
        $data = [
            'year' => $year,
            'items' => $items,
        ];
        return $this->render('yearly-archive', $data);
    }
}
