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
        return UserModel::create($login, $password);
    }
    public function signup($body)
    {
        foreach (['login', 'password'] as $name) {
            if (empty($body[$name])) {
                throw new \InvalidArgumentException(ucfirst($name) . ' is required.');
            }
        }
        $identity = UserModel::findByLogin($body['login']);
        if ($identity !== null) {
            throw new \InvalidArgumentException('Unable to register user with such username.');
        }
        UserModel::create($body['login'], $body['password']);
    }
    public function all()
    {
        $items=[];
        $users=UserModel::listAllSimple();
        foreach ($users as $v) {
            $items[] = [
                'item'=>
                [
                    'login' => $v['login'], 
                    'created_at' => date('H:i:s d.m.Y',strtotime($v['created_at']))
                ],
            ];
        }
        
        return $items;
    }
    public function listByPage($pageNum)
    {
        return UserModel::listByPage($pageNum,static::PAGINATION_INDEX);
    }
    public function simpleProfile($login)
    {
        $user=UserModel::findByLogin($login);
        if(!$user){
            return null;
        }
        return [
            'login' => $user['login'],
            'created_at' => date('H:i:s d.m.Y',strtotime($user['created_at'])),
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

        $user=UserModel::findByLogin($body['login']);
        if ($user === null) {
            throw new \InvalidArgumentException('No such user');
        }

        if (UserModel::validatePassword($user,$body['password'])) {
            throw new \InvalidArgumentException('Invalid password');
        }
        //$this->user->login($identity)
        $flag=true; // flag=update user toke
        if ($flag) {
           return $user;
        }
        throw new \InvalidArgumentException('Unable to login');
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
}
