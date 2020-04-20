<?php

namespace App\Blog\Post;

use App\Controller;
use MY\Service\BlogService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

final class PostController extends Controller
{
    protected function getId(): string
    {
        return 'blog/post';
    }

    public function index(Request $request ,LoggerInterface $logger): Response
    {
        $slug = $request->getAttribute('slug', null);
//$logger->error("XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXxxx");
        $item = BlogService::G()->getPostData($slug);
        if ($item === null) {
            return $this->responseFactory->createResponse(404);
        }

        return $this->render('index', ['item' => $item]);
    }
}
