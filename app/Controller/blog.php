<?php declare(strict_types=1);
/**
 * DuckPHP
 * From this time, you never be alone~
 */
namespace MY\Controller;

use MY\Base\Helper\ControllerHelper as C;
use MY\Service\TestService;

class blog
{
    public function __construct()
    {

    }
    public function index()
    {
        //(?<id>\d+)

        var_dump(DATE(DATE_ATOM));
        
        //C::Show(get_defined_vars());
    }
    public function post()
    {
        var_dump("!");
        var_dump(C::getParameters());

        var_dump(C::getRouteCallingMethod());
        var_dump(DATE(DATE_ATOM));
    }
    public function tag()
    {
        var_dump(C::getRouteCallingMethod());
        var_dump(DATE(DATE_ATOM));
    }
    public function archive()
    {
        var_dump(C::getRouteCallingMethod());
        var_dump(DATE(DATE_ATOM));
    }
    public function archive_yearly()
    {
var_dump(C::getRouteCallingMethod());
        var_dump(C::getParameters());
        var_dump(C::getRouteCallingMethod());
        var_dump(DATE(DATE_ATOM));
    }
    public function archive_monthly()
    {
var_dump(C::getRouteCallingMethod());
        var_dump(C::getParameters());
var_dump(DATE(DATE_ATOM));
    }
}
