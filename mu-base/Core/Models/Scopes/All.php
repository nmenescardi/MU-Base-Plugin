<?php

namespace MUBase\Core\Models\Scopes;

class All extends AbstractScope
{

  public function getArgs(): array
  {
    return [
      'posts_per_page'  => 200, //-1 ?
      'orderby'         => 'date',
      'order'           => 'ASC',
      'post_status'     => 'publish',
    ];
  }
}
