<?php

namespace App\Http\Helpers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpContext
{
    public function __construct(protected ContainerInterface $container)
    {

    }

    public function request(): ServerRequestInterface
    {
        return $this->container->get(ServerRequestInterface::class);
    }

    public function response(): ResponseHelper
    {
        return $this->container->get(ResponseHelper::class);
    }

}
