<?php

namespace MUBase\Core\Models\Posts;

use MUBase\Core\PostTypes\CptExample;

class Example extends AbstractPostRepository
{

  protected $concreteQueryScopes = [
    'related'  => \MUBase\Core\Models\Scopes\Queries\Related::class,
  ];

  public static function key(): string
  {
    return CptExample::key();
  }
}
