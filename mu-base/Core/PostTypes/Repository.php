<?php

namespace MUBase\Core\PostTypes;

class Repository
{

  protected $query;

  public function __construct(\WP_Query $query)
  {
    $this->query = $query;
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
}
