<?php

namespace MUBase\Core\Models\Scopes;

class All implements ScopeInterface
{

  protected $rawParams;

  public function __construct(array $rawParams = [])
  {
    $this->rawParams = $rawParams;

    $this->filterParams();
  }

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
