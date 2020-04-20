<?php

namespace App\Controller;

use App\Controller;
use App\Entity\User;
use App\Repository\UserRepository;
use Cycle\ORM\ORMInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Data\Reader\Sort;

class UserController extends Controller
{
    private const PAGINATION_INDEX = 5;

    protected function getId(): string
    {
        return 'user';
    }

    public function index(Request $request): Response
    {
        $pageNum = (int)$request->getAttribute('page', 1);
        $paginator = UserService::G()->listByPage($pageNum);
        return $this->render('index', ['paginator' => $paginator]);
    }

    public function profile(Request $request): Response
    {
        $login = $request->getAttribute('login', null);
        $item =  UserService::G()->profile($login);
        if ($item === null) {
            return $this->responseFactory->createResponse(404);
        }
        return $this->render('profile', ['item' => $item]);
    }
}
