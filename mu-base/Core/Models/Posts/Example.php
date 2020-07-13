<?php

namespace MUBase\Core\Models\Posts;

class Example extends AbstractPostRepository
{

  protected $concreteQueryScopes = [
    'related'  => \MUBase\Core\Models\Scopes\Queries\Related::class,
  ];

  protected static function boundCPT(): string
  {
    return \MUBase\Core\PostTypes\CptExample::class;
  }
}
