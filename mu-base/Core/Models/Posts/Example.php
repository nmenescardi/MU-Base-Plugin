<?php

namespace MUBase\Core\Models\Posts;

use MUBase\Core\PostTypes\CptExample;

class Example extends AbstractPostRepository
{

  public static function key(): string
  {
    return CptExample::key();
  }
}
