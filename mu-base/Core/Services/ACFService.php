<?php

namespace MUBase\Core\Services;

use Pimple\ServiceProviderInterface;
use Pimple\Container;

class ACFService implements ServiceProviderInterface
{

  public function register($container)
  {

    // Saves ACF's settings.
    add_filter(
      'acf/settings/save_json',
      function ($path) use ($container) {
        $path = $container['mubase_acf_json_path'];
        return $path;
      }
    );

    // Automatically loads ACF's settings.
    add_filter(
      'acf/settings/load_json',
      function ($path) use ($container) {
        $path[] = $container['mubase_acf_json_path'];
        return $path;
      }
    );
  }
}
