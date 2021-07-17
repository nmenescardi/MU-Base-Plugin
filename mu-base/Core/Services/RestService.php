<?php

namespace MUBase\Core\Services;

use Pimple\ServiceProviderInterface;
use MUBase\Core\Rest\Responses\Response;

class RestService implements ServiceProviderInterface
{
    public function register($container)
    {
      
      $container['routes.response'] = function($container) {
        return new Response();
      };

      $this->initRoutes([
        \MUBase\Core\Rest\Routes\ExampleRoute::class
      ]);
    }

    protected function initRoutes(array $routeClasses)
    {
      foreach($routeClasses as $routeClass){
        (new $routeClass)->init();
      }
    }
}