<?php

namespace MUBase\Core\Models\Scopes\Query;

class Latest extends AbstractScope
{


  protected function filterParams()
  {
    $this->postsPerPage =
      (is_array($this->rawParams) && isset($this->rawParams[0]))
      ? $this->rawParams[0]
      : false;
  }

  protected function concreteArgs(): array
  {
    return [
      'posts_per_page'  => $this->postsPerPage ?: 15,
      'orderby'         => 'date',
      'order'           => 'DESC',
    ];
  }
}
