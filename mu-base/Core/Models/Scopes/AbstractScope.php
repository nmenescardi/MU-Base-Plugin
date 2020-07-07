<?php

namespace MUBase\Core\Models\Scopes;

abstract class AbstractScope
{

  abstract protected function concreteArgs(): array;

  protected $rawParams;

  protected $model;

  public function __construct(array $rawParams = [], $model)
  {
    $this->rawParams = $rawParams;

    $this->model = $model;

    $this->filterParams();
  }

  /**
   * This method can be overridden by a concrete Scope to handling dynamic params.
   *
   * @return void
   */
  protected function filterParams()
  {
  }

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
