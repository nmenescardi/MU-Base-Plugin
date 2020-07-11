<?php

namespace MUBase\Core\Models\Scopes;

use MUBase\Core\Models\Scopes\Checks\AbstractCheckScope;
use MUBase\Core\Models\Scopes\Queries\AbstractQueryScope;

trait HasScopeTrait
{
  protected $scopes;

  protected function initScopes()
  {
    $this->queryScopes = array_merge(
      [
        'all'         => \MUBase\Core\Models\Scopes\Queries\All::class,
        'latest'      => \MUBase\Core\Models\Scopes\Queries\Latest::class,
        'byAuthor'    => \MUBase\Core\Models\Scopes\Queries\ByAuthor::class,
        'byID'        => \MUBase\Core\Models\Scopes\Queries\ByID::class,
      ],
      $this->concreteQueryScopes ?? []
    );

    $this->checkScopes = array_merge(
      [
        'isSameType'  => \MUBase\Core\Models\Scopes\Checks\IsSameType::class,
      ],
      $this->concreteCheckScopes ?? []
    );
  }

  public function __call($method, $args)
  {
    // Query Scopes
    if (isset($this->queryScopes[$method]))
      return $this->handleQueryScope(
        new $this->queryScopes[$method]($args, $this)
      );

    // Check Scopes
    if (isset($this->checkScopes[$method]))
      return $this->handleCheckScope(
        new $this->checkScopes[$method]($args, $this)
      );

    // Scope Not found
    throw new \BadMethodCallException;
  }

  protected function handleQueryScope(AbstractQueryScope $scope)
  {
    $args = $scope->getArgs();

    return $this->find($args);
  }

  protected function handleCheckScope(AbstractCheckScope $scope)
  {
    return $scope->evaluate();
  }
}
