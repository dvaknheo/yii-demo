<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Base;
use DuckPhp\ThrowOn;

class ExitException extends \Exception
{
    use ThrowOn;
}