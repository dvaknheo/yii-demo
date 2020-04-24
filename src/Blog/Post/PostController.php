<?php

namespace App\Blog\Post;

use App\Controller;
use App\Service\BlogService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class PostController extends Controller
{
    protected function getId(): string
    {
        return 'blog/post';
    }

    public function index(Request $request): Response
    {
        //BlogService::G()->initSQLlogger();
        
        $slug = $request->getAttribute('slug', null);
        $item = BlogService::G()->getPostData($slug);
        if ($item === null) {
            return $this->responseFactory->createResponse(404);
        }

        return $this->render('index', ['item' => $item]);
    }
}
