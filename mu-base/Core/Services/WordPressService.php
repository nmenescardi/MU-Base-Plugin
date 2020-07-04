<?php

namespace MUBase\Core\Services;

use Pimple\ServiceProviderInterface;

class WordPressService implements ServiceProviderInterface
{

  public function register($container)
  {
    $container['wp.query'] = $container->factory(function ($c) {
      return new \WP_Query();
    });
  }
}
