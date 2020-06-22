<?php

namespace MUBase\Core\Helpers;

use MUBase\Core\DependencyInjection\Container;

if (!function_exists('app')) {
  /**
   * Get the available container instance.
   *
   * @param string $abstract
   * @param array $value
   *
   * @return mixed
   */
  function app($abstract = null, array $value = [])
  {
    if ($abstract === null) {
      return Container::getInstance();
    }

    return Container::getInstance()->offsetExists($abstract)
      ? Container::getInstance()->offsetGet($abstract)
      : Container::getInstance()->instance($abstract, $value);
  }
}
