<?php

namespace MUBase\Core\Models\Scopes;

class Latest implements ScopeInterface
{

  public function getArgs()
  {
    return [
      'posts_per_page'  => 15,
      'orderby'         => 'date',
      'order'           => 'DESC',
      'post_status'     => 'publish',
    ];
  }
}
