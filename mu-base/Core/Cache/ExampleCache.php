<?php

namespace MUBase\Core\Cache;

class ExampleCache extends AbstractCache
{

  protected function key(): string
  {
    return 'ExampleCacheKey';
  }
}
