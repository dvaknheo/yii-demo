<?php

declare(strict_types=1);

namespace App\Blog;

use App\Controller;
use MY\Service\BlogService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class BlogController extends Controller
{
    protected function getId(): string
    {
        return 'blog';
    }
    public function index(Request $request): Response
    {
        $pageNum = (int)$request->getAttribute('page', 1);
        $data = BlogService::G()->getDataToIndex($pageNum);
        
        return $this->render('index', $data);
    }
}
