<?php

namespace MUBase\Core\Models\Scopes;

class Latest extends AbstractScope
{

  public function getArgs(): array
  {
    return [
      'posts_per_page'  => 15,
      'orderby'         => 'date',
      'order'           => 'DESC',
      'post_status'     => 'publish',
    ];
  }
}
