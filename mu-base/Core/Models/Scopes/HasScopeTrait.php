<?php

namespace MUBase\Core\Models\Scopes;

trait HasScopeTrait
{

  protected $scopes;


  protected function initScopes()
  {
    $this->scopes = array_merge(
      [
        'all'         => \MUBase\Core\Models\Scopes\All::class,
        'latest'      => \MUBase\Core\Models\Scopes\Latest::class,
        'byAuthor'    => \MUBase\Core\Models\Scopes\ByAuthor::class,
      ],
      $this->concrete_scopes ?? []
    );
  }

  public function __call($method, $args)
  {
    if (!isset($this->scopes[$method])) throw new \BadMethodCallException;

    $scope = new $this->scopes[$method]($args);

    if (!$scope instanceof AbstractScope) throw new \BadMethodCallException;

    $args = ($scope)->getArgs();

    return $this->find($args);
  }
}
