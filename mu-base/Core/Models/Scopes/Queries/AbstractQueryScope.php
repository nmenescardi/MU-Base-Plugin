<?php

namespace MUBase\Core\Models\Scopes\Queries;

use MUBase\Core\Models\Scopes\AbstractScope;

abstract class AbstractQueryScope extends AbstractScope
{

  abstract protected function concreteArgs(): array;

  public function getArgs(): array
  {
    return array_merge(
      [
        'posts_per_page'  => 200,
        'orderby'         => 'date',
        'order'           => 'ASC',
        'post_status'     => 'publish',
      ],
      $this->concreteArgs()
    );
  }
}
