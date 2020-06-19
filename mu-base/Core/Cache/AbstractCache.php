<?php

namespace MUBase\Core\Cache;

abstract class AbstractCache
{
  abstract protected function key(): string;

  protected function lifespan()
  {
    if (defined('WP_AVOID_TRANSIENT'))
      return 1;
    else
      return $this->customLifespan();
  }

  protected function customLifespan()
  {
    return DAY_IN_SECONDS;
  }

  public function getData()
  {
    return get_transient($this->key());
  }

  public function update($data)
  {
    set_transient(
      $this->key(),
      $data,
      $this->lifespan()
    );
  }

  public function flush()
  {
    delete_transient($this->key());
  }
}
