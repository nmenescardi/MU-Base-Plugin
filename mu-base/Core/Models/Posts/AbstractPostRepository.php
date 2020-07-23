<?php

namespace MUBase\Core\Models\Posts;

use MUBase\Core\Models\Scopes\HasScopeTrait as HasScope;
use MUBase\Core\Models\Meta\HasMetaTrait as HasMeta;
use stdClass;

use function MUBase\Core\Helpers\app;

abstract class AbstractPostRepository
{

  use HasScope, HasMeta;

  abstract protected static function boundCPT(): string;

  protected $properties;

  protected $query;


  public function __construct()
  {
    $this->query = app('wp.query');

    $this->cpt_taxonomies = app('cpt_taxonomies')[self::key()] ?? [];

    $this->initScopes();

    $this->initPropertiesMap();

    $this->initDefaultProps();
  }

  public static function key(): string
  {
    return static::boundCPT()::key();
  }

  public function save(array $custom_props = [])
  {
    $properties = $this->mergeProperties($custom_props);

    if (isset($properties['ID']) && !$properties['ID'])
      return $this->fillUpdatedPost(
        wp_update_post($properties, true)
      );

    return $this->fillUpdatedPost(
      wp_insert_post($properties, true)
    );
  }

  protected function fillUpdatedPost($post_id)
  {
    if (!is_numeric($post_id) || $post_id < 1) return;

    $this->fill(get_post($post_id));

    return $post_id;
  }

  public function __get($property)
  {
    // Is the property a taxonomy attached to CPT? 
    if (array_key_exists($property, $this->cpt_taxonomies))
      return get_the_terms($this->ID, $this->cpt_taxonomies[$property]) ?: [];


    // Is the Property a registered metadata?
    if ($metaArgs = $this->getSingleMetaArgs($property))
      return
        $this->getMeta(
          $property, // Key
          $metaArgs['single'] ?? true
        );
  }

  /**
   * @param WP_Post|Model|int $post 
   * @param boolean $force
   * @return WP_Post|false|null — Post data on success, false or null on failure.
   */
  public function delete($post = null, $force = false)
  {
    if (is_numeric($post) && $post > 0)
      $post_id = $post;
    elseif (isset($post->ID))
      $post_id = $post->ID;
    else
      $post_id = $this->ID;

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

    return $this->convertToModel(
      $this->query->query($args),
      $this->isSingleValueQuery($args)
    );
  }

  protected function convertToModel($posts, $singleQuery = false)
  {
    if ($singleQuery)
      return !empty($posts[0]) ? $this->fill($posts[0]) : null;

    return array_map(function ($post) {
      return (new static())->fill($post);
    }, $posts);
  }

  protected function fill(\WP_Post $post)
  {
    foreach ($this->properties_map as $WP_prop => $object_prop) {
      $this->$object_prop = $post->$WP_prop;
    }

    return $this;
  }

  protected function isSingleValueQuery($args)
  {
    return
      isset($args['posts_per_page']) &&
      ($args['posts_per_page'] === 1 || $args['posts_per_page'] === '1');
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
        'post_name'     => 'slug',
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
    $result = [];

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

    //Fill With Meta
    if (!empty($metaArgs = $this->getMetaArgs()))
      $result['meta_input'] = $metaArgs;

    return $result;
  }

  public function toPost()
  {
    $stdObject = new stdClass();

    // Fill new stdObject needed to instantiate the new WP_Post
    foreach ($this->properties_map as $WP_prop => $object_prop) {
      $stdObject->{$WP_prop} = $this->{$object_prop};
    }

    return new \WP_Post($stdObject);
  }
}
