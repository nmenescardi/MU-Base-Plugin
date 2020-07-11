<?php

namespace MUBase\Core\Models\Scopes\Query;

class ByID extends AbstractScope
{

  protected function filterParams()
  {
    $this->postID =
      (is_array($this->rawParams) && isset($this->rawParams[0]))
      ? $this->rawParams[0]
      : false;
  }

  protected function concreteArgs(): array
  {
    return [
      'p'  => $this->postID,
      'posts_per_page'  => 1,
    ];
  }
}
