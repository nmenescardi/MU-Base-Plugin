<?php

namespace MUBase\Core\Models\Scopes;

class All extends AbstractScope
{

  protected function concreteArgs(): array
  {
    return [
      'posts_per_page'  => -1
    ];
  }
}
