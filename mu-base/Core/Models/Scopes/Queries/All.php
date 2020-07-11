<?php

namespace MUBase\Core\Models\Scopes\Queries;

class All extends AbstractQueryScope
{

  protected function concreteArgs(): array
  {
    return [
      'posts_per_page'  => -1
    ];
  }
}
