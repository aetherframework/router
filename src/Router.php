<?php

namespace AEtherFramework;

use AEtherFramework\Config\Adapter\ConfigData;
use AEtherFramework\Http\Request;
use AEtherFramework\Router\InvalidRouteType;
use AEtherFramework\Router\Parameter;
use AEtherFramework\Router\RegexRoute;
use AEtherFramework\Router\RouteInterface;
use AEtherFramework\Router\RouteNotFound;
use AEtherFramework\Router\StaticRoute;

class Router
{
    /** @var RouteInterface[] */
    protected $routes;

    /**
     * Router constructor.
     * @param ConfigData[] $routes
     * @throws InvalidRouteType
     */
    public function __construct(array $routes)
    {
        foreach ($routes as $route) {
            $routeObject = $this->getRouteClass($route->type);
            $routeObject
                ->setPath($route->path)
                ->setController($route->controller)
                ->setVerb($route->verb);
            foreach ($route->parameters as $parameter) {
                $parameterObject = new Parameter();
                $parameterObject
                    ->setName($parameter->name)
                    ->setAllowedValues($parameter->allowedValues)
                    ->setRequired($parameter->required);
                $routeObject->addParameters($parameterObject);
            }
            $this->routes[] = $routeObject;

        }
    }

    /**
     * @param $type
     * @return RouteInterface
     * @throws InvalidRouteType
     */
    protected function getRouteClass($type): RouteInterface
    {
        switch ($type) {
            case "regex":
                return new RegexRoute;
                break;
            case "static":
                return new StaticRoute;
                break;
            default:
                throw new InvalidRouteType(sprintf(
                    '%s is not a known route type',
                    $type
                ));
        }
    }

    public function match(Request $request) {
        foreach ($this->routes as $route) {
            if ($route->match($request)) {
                return $route;
            }
        }
        throw new RouteNotFound("No route matching request could be found");
    }
}