<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Base;

use DuckPhp\SingletonEx;

// use DuckPhp\Base\StrictServiceTrait;
use Cycle\ORM\ORMInterface;

class BaseService
{
    use SingletonEx;
    
    public function getORM()
    {
        global $container;
        global $promise;
        if(!empty($promise)){
            return $promise->getORM();
        }
        return $container->get(ORMInterface::class);
    }
    public function getObject($class)
    {
        global $container;
        return $container->get($class);
    }
}
