<?php

namespace MUBase\Core\Models\Scopes;

abstract class AbstractScope
{
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
}
