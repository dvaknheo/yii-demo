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
        $container=App::G()->container;
        $promise=App::G()->promise;
        if(!empty($promise)){
            return $promise->getORM();
        }
        return $container->get(ORMInterface::class);
    }
    public function getObject($class)
    {
        $container=App::G()->container;
        return $container->get($class);
    }
}
