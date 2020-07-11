<?php

namespace MUBase\Core\Models\Scopes;

use MUBase\Core\Models\Scopes\Checks\AbstractCheckScope;
use MUBase\Core\Models\Scopes\Queries\AbstractQueryScope;

trait HasScopeTrait
{
  protected $scopes;

  protected function initScopes()
  {
    $this->queryStaticScopes = array_merge(
      [
        'all'         => \MUBase\Core\Models\Scopes\Queries\All::class,
        'latest'      => \MUBase\Core\Models\Scopes\Queries\Latest::class,
        'byAuthor'    => \MUBase\Core\Models\Scopes\Queries\ByAuthor::class,
        'byID'        => \MUBase\Core\Models\Scopes\Queries\ByID::class,
        'find'        => \MUBase\Core\Models\Scopes\Queries\ByID::class, // Alias
      ],
      $this->concreteQueryStaticScopes ?? []
    );

    $this->queryScopes = array_merge(
      [],
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

  public static function __callStatic($method, $args)
  {
    $model = new static();

    // Query Static Scopes
    if (isset($model->queryStaticScopes[$method]))
      return $model->handleQueryScope(
        new $model->queryStaticScopes[$method]($args, $model)
      );

    // Scope Not found
    throw new \BadMethodCallException;
  }

  protected function handleQueryScope(AbstractQueryScope $scope)
  {
    return $this->performQuery($scope->getArgs());
  }

  protected function handleCheckScope(AbstractCheckScope $scope)
  {
    return $scope->evaluate();
  }
}
