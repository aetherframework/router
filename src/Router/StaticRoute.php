<?php

namespace AEtherFramework\Router;

use AEtherFramework\Http\RequestInterface;

class StaticRoute implements RouteInterface
{
    protected $path;
    protected $verb;
    protected $controller;
    /**
     * @var Parameter[]
     */
    protected $parameters;

    public function __construct()
    {
        $this->parameters = [];
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getVerb(): string
    {
        return $this->verb;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @return Parameter[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function match(RequestInterface $request): bool
    {
        if ($this->getVerb() != $request->getVerb()) {
            return false;
        }
        return $this->getPath() == trim($request->getPath(), '/');
    }

    /**
     * @inheritDoc
     */
    public function setPath($path): RouteInterface
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setVerb($verb): RouteInterface
    {
        $this->verb = $verb;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setController($controller): RouteInterface
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addParameters(Parameter $parameter): RouteInterface
    {
        $this->parameters[$parameter->getName()] = $parameter;
        return $this;
    }
}
