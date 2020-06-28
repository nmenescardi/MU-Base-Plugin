<?php

namespace MUBase\Core\Models\Scopes;

class Latest extends AbstractScope
{

  protected function concreteArgs(): array
  {
    return [
      'posts_per_page'  => 15,
      'orderby'         => 'date',
      'order'           => 'DESC',
    ];
  }
}
