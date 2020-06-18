<?php

namespace MUBase\Core\DependencyInjection;

class Container extends \Pimple\Container
{

  public function registerServices(array $services)
  {
    foreach ($services as $service) {
      $this->register($service);
    }
  }
}
