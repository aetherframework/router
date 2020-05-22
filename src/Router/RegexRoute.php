<?php

namespace AEtherFramework\Router;

use AEtherFramework\Http\RequestInterface;

class RegexRoute implements RouteInterface
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
        if ($request->getVerb() != $this->getVerb()) {
            return false;
        }
        $requestPath = $request->getPath();
        if (!preg_match('(^' . $this->getPath() . ')', $requestPath, $matches)) {
            return false;
        }
        usort($matches, function($a, $b) {
            $aLen = strlen($a);
            $bLen = strlen($b);
            if ($aLen == $bLen) {
                return 0;
            }
            return $aLen < $bLen;
        });
        foreach($matches as $match) {
            $requestPath = str_replace(rtrim($match, '/'), '', $requestPath);
        }
        $params = [];
        foreach ($this->getParameters() as $parameter) {
            $pattern = $parameter->getName() . '\/' . $parameter->getAllowedValues();
            $isMatched = preg_match('((/' . $pattern . ')(\/)?)', $requestPath, $matches);
            if (!$isMatched && $parameter->isRequired()) {
                return false;
            }
            if ($isMatched) {
                $requestPath = str_replace($matches[1], '', $requestPath);
                list($name, $value) = explode('/', ltrim($matches[1], '/'));
                $parameter->setValue($value);
                $params[] = $parameter;
            }
        }
        $this->parameters = $params;
        return '' == $requestPath || '/' == $requestPath;
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
