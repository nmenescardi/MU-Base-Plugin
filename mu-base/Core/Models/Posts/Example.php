<?php

namespace MUBase\Core\Models\Posts;

use MUBase\Core\PostTypes\CptExample;

class Example extends AbstractPostRepository
{

  protected $concrete_scopes = [
    'related'  => \MUBase\Core\Models\Scopes\Related::class,
  ];

  public static function key(): string
  {
    return CptExample::key();
  }
}
