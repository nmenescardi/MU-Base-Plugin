<?php

namespace MUBase\Core\Models\Posts;

use MUBase\Core\Models\Scopes\{All, ScopeInterface};

abstract class AbstractPostRepository
{

  abstract public static function key(): string;


  protected $scopes;

  protected $query;


  public function __construct(\WP_Query $query)
  {
    $this->query = $query;

    $this->initScopes();
  }

  public function save(array $post)
  {
    if (!$post['ID']) return wp_update_post($post, true);

    return wp_insert_post($post, true);
  }

  public function remove(\WP_Post $post, $force = false)
  {
    wp_delete_post($post->ID, $force);
  }


  public function findByAuthor(\WP_User $author, $limit = 10)
  {
    return $this->find([
      'author' => $author->ID,
      'posts_per_page' => $limit
    ]);
  }

  public function findById($id)
  {
    return $this->findOne(['p' => $id]);
  }

  protected function find(array $args)
  {
    $args = array_merge([
      'no_found_rows' => true,
      'update_post_meta_cache' => true,
      'update_post_term_cache' => false,
    ], $args);

    return $this->query->query($args);
  }

  protected function findOne(array $args)
  {
    $args = array_merge($args, [
      'posts_per_page' => 1
    ]);

    $post = $this->find($args);

    return !empty($post[0]) ? $post[0] : null;
  }


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

    return []; //TODO: query using repository
  }
}
