<?php

namespace MUBase\Core\Models\Scopes\Query;

class All extends AbstractScope
{

  protected function concreteArgs(): array
  {
    return [
      'posts_per_page'  => -1
    ];
  }
}
