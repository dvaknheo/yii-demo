<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace App\Service;


use Cycle\ORM\ORMInterface;
use Psr\Container\ContainerInterface;

class BaseService
{
    
    protected static $container;
    
    
    // copy from other same as GetInstance;
    protected static $_instances = [];
    public static function G($object = null)
    {
        if (defined('__SINGLETONEX_REPALACER')) {
            $callback = __SINGLETONEX_REPALACER;
            return ($callback)(static::class, $object);
        }
        //fwrite(STDOUT,"SINGLETON ". static::class ."\n");
        if ($object) {
            self::$_instances[static::class] = $object;
            return $object;
        }
        $me = self::$_instances[static::class] ?? null;
        if (null === $me) {
            $me = new static();
            self::$_instances[static::class] = $me;
        }
        
        return $me;
    }
    
    public static function SetContainer($container)
    {
        self::$container=$container;
        self::G()->initSQLlogger();
    }
    public static function SetPromise($promise)
    {
        $ref=new \ReflectionClass($promise);
        $prop=$ref->getProperty('container');
        $prop->setAccessible(true);
        $container=$prop->getValue($promise);
        self::$container=$container;
        
        self::G()->initSQLlogger();
    }
    public function getORM()
    {
        $container=static::$container;
        return $container->get(ORMInterface::class);
    }
    public function getObject($class)
    {
        $container=static::$container;
        return $container->get($class);
    }
    
    public function initSQLlogger()
    {
        $container=self::$container;//$this->getObject(ContainerInterface::class);
        
        $dm=$this->getObject('Spiral\\Database\\DatabaseManager');
        $logger=$this->getObject('Psr\\Log\\LoggerInterface');
        $dm->getDrivers()[0]->setLogger($logger);
    }
}
