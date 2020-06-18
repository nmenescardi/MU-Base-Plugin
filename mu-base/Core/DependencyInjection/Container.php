<?php

namespace MUBase\Core\DependencyInjection;

use Pimple\ServiceProviderInterface;

class Container extends \Pimple\Container
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
