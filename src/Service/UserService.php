<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */

namespace App\Service;

use App\Entity\User;
use Cycle\ORM\Transaction;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Yii\Web\User\User as WebUser;
use Yiisoft\Mailer\MailerInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Data\Paginator\OffsetPaginator;

class UserService extends BaseService
{
    private const PAGINATION_INDEX = 5;

    public function create($login,$password)
    {
        $user = new User($login, $password);
        $transaction = new Transaction($this->getORM());
        $transaction->persist($user);
        $transaction->run();
    }
    public function all()
    {
        $userRepo = $this->getORM()->getRepository(User::class);

        $dataReader = $userRepo->findAll()->withSort((new Sort([]))->withOrderString('login'));
        $users = $dataReader->read();
        $items = [];
        foreach ($users as $user) {
            $items[] = ['login' => $user->getLogin(), 'created_at' => $user->getCreatedAt()->format('H:i:s d.m.Y')];
        }
        
        return $items;
    }
    public function listByPage($pageNum)
    {
        $userRepo = $this->getORM()->getRepository(User::class);
        $dataReader = $userRepo->findAll()->withSort((new Sort([]))->withOrderString('login'));
        $paginator = (new OffsetPaginator($dataReader))
            ->withPageSize(self::PAGINATION_INDEX)
            ->withCurrentPage($pageNum);
        return $paginator;
    }
    public function simpleProfile($login)
    {
        $userRepository = $this->getORM()->getRepository(User::class);
        $user = $userRepository->findByLogin($login);
        if ($user === null) {
            return [];
        }

        return [
            'login' => $user->getLogin(),
            'created_at' => $user->getCreatedAt()->format('H:i:s d.m.Y'),
        ];
    }
    public function profile($login)
    {
        $userRepository = $this->getORM()->getRepository(User::class);
        $user = $userRepository->findByLogin($login);
        return $user;
    }
    
    public function login($body)
    {
        $error = null;
        
        foreach (['login', 'password'] as $name) {
            if (empty($body[$name])) {
                throw new \InvalidArgumentException(ucfirst($name) . ' is required');
            }
        }

        $identity = $this->getObject(IdentityRepositoryInterface::class)->findByLogin($body['login']);
        if ($identity === null) {
            throw new \InvalidArgumentException('No such user');
        }

        if (!$identity->validatePassword($body['password'])) {
            throw new \InvalidArgumentException('Invalid password');
        }
        if ($this->getObject(WebUser::class)->login($identity)) {
           return true;
        }
        throw new \InvalidArgumentException('Unable to login');
    }
    public function logout()
    {
        $this->getObject(WebUser::class)->logout();
    }
    public function sendMail($body, $file,$to,$from)
    {
        foreach (['subject', 'name', 'email', 'content'] as $name) {
            if (empty($body[$name])) {
                throw new \InvalidArgumentException(ucfirst($name) . ' is required');
            }
        }
        $message = $this->getObject(MailerInterface::class)->compose(
            'contact',
            [
                'name' => $body['name'],
                'email' => $body['email'],
                'content' => $body['content'],
            ]
        )
            ->setSubject($body['subject'])
            ->setTo($to)
            ->setFrom($from);

        if (!empty($file)) {
            $message->attachContent(
                (string)$file->getStream(),
                [
                    'fileName' => $file->getClientFilename(),
                    'contentType' => $file->getClientMediaType(),
                ]
            );
        }
        $message->send();
    }
    public function signup($body)
    {
        foreach (['login', 'password'] as $name) {
            if (empty($body[$name])) {
                throw new \InvalidArgumentException(ucfirst($name) . ' is required.');
            }
        }
        $identity = $this->getObject(IdentityRepositoryInterface::class)->findByLogin($body['login']);
        if ($identity !== null) {
            throw new \InvalidArgumentException('Unable to register user with such username.');
        }

        $user = new WebUser($body['login'], $body['password']);

        $transaction = new Transaction($orm);
        $transaction->persist($user);

        $transaction->run();
    }
}
