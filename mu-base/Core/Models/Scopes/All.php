<?php

namespace MUBase\Core\Models\Scopes;

class All implements ScopeInterface
{

  public function getArgs()
  {
    return [
      'posts_per_page'  => 200, //-1 ?
      'orderby'         => 'date',
      'order'           => 'ASC',
      'post_status'     => 'publish',
    ];
  }
}
