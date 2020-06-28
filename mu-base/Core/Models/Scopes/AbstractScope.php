<?php

namespace MUBase\Core\Models\Scopes;

abstract class AbstractScope
{

  abstract public function getArgs(): array;

  protected $rawParams;

  public function __construct(array $rawParams = [])
  {
    $this->rawParams = $rawParams;

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
