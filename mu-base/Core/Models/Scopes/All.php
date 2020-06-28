<?php

namespace MUBase\Core\Models\Scopes;

class All extends AbstractScope
{

  protected function filterParams()
  {
    //TODO: Check the first Param is an ID or array of IDs to filter
  }

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
