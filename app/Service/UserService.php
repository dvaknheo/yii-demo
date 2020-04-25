<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */

namespace MY\Service;

use MY\Base\BaseService;
use MY\Base\Helper\ServiceHelper as S;
use MY\Model\UserModel;
use MY\Base\App;



class UserService extends BaseService
{
    private const PAGINATION_INDEX = 5;

    public function create($login,$password)
    {
        //INSERT INTO `user` (`token`, `login`, `password_hash`, `created_at`, `updated_at`) VALUES ('XXX', 'aaaaa4', 'BBB', 'cccc', 'dddd');
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
        $user=UserModel::listByPage($pageNum);
        return [[],0];
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
        $user=UserModel::findByLogin($login);
        return $user;
    }
    
    public function login($body)
    {
        $body = $request->getParsedBody();
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
        if ($this->user->login($identity)) {
           return true;
        }
        throw new \InvalidArgumentException('Unable to login');
    }
    public function logout()
    {
        $this->getObject(WebUser::class)->logout();
    }
    public function sendMail($body, $file)
    {
        $to = $this->parameters->get('supportEmail');
        $from = $this->parameters->get('mailer.username');
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

        $user = new User($body['login'], $body['password']);

        $transaction = new Transaction($orm);
        $transaction->persist($user);

        $transaction->run();
    }
}
