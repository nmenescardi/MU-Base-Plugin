<?php

namespace MUBase\Core\Services;

use MUBase\Core\Cache\ExampleCache;
use Pimple\ServiceProviderInterface;

class CacheService implements ServiceProviderInterface
{

  public function register($container)
  {
    $container['cache.example'] = function ($container) {
      return new ExampleCache;
    };
  }
}
