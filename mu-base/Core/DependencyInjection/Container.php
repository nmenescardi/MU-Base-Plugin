<?php

namespace MUBase\Core\DependencyInjection;

use Pimple\ServiceProviderInterface;
use Pimple\Container as BaseContainer;

class Container extends BaseContainer
{

  public function registerServices(array $services)
  {
    foreach ($services as $service) {

      $service = new $service();

      if (!$service instanceof ServiceProviderInterface)
        throw new \InvalidArgumentException('service object must implement the "ServiceProviderInterface".');

      $this->register($service);
    }
  }
}
