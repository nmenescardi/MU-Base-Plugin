<?php

namespace MUBase\Core\Models\Posts;

use MUBase\Core\Models\Scopes\{All, ScopeInterface, HasScopeTrait};

use function MUBase\Core\Helpers\app;

abstract class AbstractPostRepository
{

  use HasScopeTrait;

  abstract public static function key(): string;

  protected $properties;

  protected $query;


  public function __construct()
  {
    $this->query = app('wp.query');

    $this->initScopes();

    $this->initPropertiesMap();

    $this->initDefaultProps();
  }

  public function save(array $custom_props = [])
  {
    $properties = $this->mergeProperties($custom_props);

    if (isset($properties['ID']) && !$properties['ID'])
      return wp_update_post($properties, true);

    return $this->fillUpdatedPost(
      wp_insert_post($properties, true)
    );
  }

  protected function fillUpdatedPost($post_id)
  {
    if (!is_numeric($post_id) || $post_id < 1) return;

    $updated_post = get_post($post_id);

    foreach ($this->properties_map as $WP_prop => $object_prop) {
      $this->$object_prop = $updated_post->$WP_prop;
    }

    return $post_id;
  }

  /**
   * @param WP_Post|int $post 
   * @param boolean $force
   * @return WP_Post|false|null — Post data on success, false or null on failure.
   */
  public function delete($post = null, $force = false)
  {
    $post_id = $post instanceof \WP_Post ? $post->ID : $post;

    if (!$post_id && isset($this->ID)) $post_id = $this->ID;

    return wp_delete_post($post_id, $force);
  }

  protected function performQuery(array $args)
  {
    $args = array_merge([
      'post_type' => static::key(),
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

    $post = $this->performQuery($args);

    return !empty($post[0]) ? $post[0] : null;
  }

  protected function initPropertiesMap()
  {
    $this->properties_map = array_merge(
      $this->props_map ?? [],
      [
        'ID'            => 'ID',
        'post_title'    => 'title',
        'post_type'     => 'type',
        'post_status'   => 'status',
        'post_content'  => 'content',
        'post_author'   => 'author',
        'post_date'     => 'date',
      ]
    );
  }

  protected function initDefaultProps()
  {
    $this->default_props = [
      'post_type'   => static::key(),
      'post_status' => 'publish',
    ];
  }

  protected function mergeProperties(array $custom_props = [])
  {
    $result = []; //TODO: refactor it using array_map

    foreach ($this->properties_map as $WP_prop => $object_prop) {

      // 1- Priority to custom props
      if (isset($custom_props[$WP_prop])) {
        $result[$WP_prop] = $custom_props[$WP_prop];
        continue;
      }

      // 2- Having the value as object property in the model.
      if (isset($this->$object_prop)) {
        $result[$WP_prop] = $this->$object_prop;
        continue;
      }

      // 3- Having some value as default.
      if (isset($this->default_props[$WP_prop])) {
        $result[$WP_prop] = $this->default_props[$WP_prop];
      }
    }

    return $result;
  }
}
