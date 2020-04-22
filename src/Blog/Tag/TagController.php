<?php

namespace App\Blog\Tag;

use App\Controller;
use App\Service\BlogService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class TagController extends Controller
{
    private const POSTS_PER_PAGE = 10;

    protected function getId(): string
    {
        return 'blog/tag';
    }

    public function index(Request $request): Response
    {
        $label = $request->getAttribute('label', null);
        $pageNum = (int)$request->getAttribute('page', 1);

        $data =  BlogService::G()->getTagData($label, $pageNum);
        if ($data['item'] === null) {
            return $this->responseFactory->createResponse(404);
        }
        return $this->render('index', $data);
    }
}
