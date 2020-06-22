<?php

namespace MUBase\Core\DependencyInjection;

use Pimple\ServiceProviderInterface;
use Pimple\Container as BaseContainer;

class Container extends BaseContainer
{

  /**
   * @var Container
   */
  protected static $instance = null;


  /**
   * @return Container|null
   */
  public static function getInstance(array $values = [])
  {
    if (static::$instance === null)
      static::$instance = new self($values);

    return static::$instance;
  }

  private function __construct(array $values = [])
  {
    parent::__construct($values);
  }

  public function registerServices(array $services)
  {
    foreach ($services as $service) {

      $service = new $service();

      if (!$service instanceof ServiceProviderInterface)
        throw new \InvalidArgumentException('service object must implement the "ServiceProviderInterface".');

      $this->register($service);
    }
  }

  public function get($id)
  {
    return $this->offsetGet($id);
  }

  public function has($id)
  {
    return $this->offsetExists($id);
  }

  public function __get($key)
  {
    return $this[$key];
  }

  public function __set($key, $value)
  {
    $this[$key] = $value;
  }

  public function __isset($key)
  {
    return isset($this[$key]);
  }
}
