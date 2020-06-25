<?php

namespace MUBase\Core\Models\Scopes;

trait HasScopeTrait
{

  protected $scopes;


  protected function initScopes()
  {
    $this->scopes = array_merge(
      $this->concrete_scopes ?? [],
      [
        'all'         => \MUBase\Core\Models\Scopes\All::class,
        'latest'      => \MUBase\Core\Models\Scopes\Latest::class,
        'by_authors'  => \MUBase\Core\Models\Scopes\ByAuthors::class,
      ]
    );
  }

  public function __call($method, $args)
  {
    if (!isset($this->scopes[$method])) throw new \BadMethodCallException;

    $scope = new $this->scopes[$method]($args);

    if (!$scope instanceof ScopeInterface) throw new \BadMethodCallException;

    $args = ($scope)->getArgs();

    return $args; //TODO: query using repository. 
  }
}
