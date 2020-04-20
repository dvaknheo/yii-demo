<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */

namespace MY\Service;

use MY\Base\BaseService;
use MY\Base\Helper\ServiceHelper as S;

use App\Entity\User;
use Cycle\ORM\Transaction;

class UserService extends BaseService
{
    public function create($login,$password)
    {
        $user = new User($login, $password);
        $transaction = new Transaction($this->getORM());
        $transaction->persist($user);
        $transaction->run();
    }
}
