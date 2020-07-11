<?php

namespace MUBase\Core\Models\Scopes;

trait HasScopeTrait
{
  protected $scopes;

  protected function initScopes()
  {
    $this->queryScopes = array_merge(
      [
        'all'         => \MUBase\Core\Models\Scopes\Query\All::class,
        'latest'      => \MUBase\Core\Models\Scopes\Query\Latest::class,
        'byAuthor'    => \MUBase\Core\Models\Scopes\Query\ByAuthor::class,
        'byID'        => \MUBase\Core\Models\Scopes\Query\ByID::class,
      ],
      $this->concreteQueryScopes ?? []
    );
  }

  public function __call($method, $args)
  {
    if (!isset($this->queryScopes[$method])) throw new \BadMethodCallException;

    $scope = new $this->queryScopes[$method]($args, $this);

    if (!$scope instanceof Query\AbstractScope) throw new \BadMethodCallException;

    $args = ($scope)->getArgs();

    return $this->find($args);
  }
}
