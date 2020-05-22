<?php


namespace AEtherFramework\Router;


class InvalidRouteType extends \Exception
{
    public function __construct($message = "", $code = 500, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}