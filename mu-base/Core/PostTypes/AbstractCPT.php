<?php

namespace MUBase\Core\PostTypes;

abstract class AbstractCPT
{

  abstract public static function key(): string;

  abstract public function args(): array;


  public function init()
  {
    add_action('init', array($this, 'registerCPT'));
    add_action('init', array($this, 'registerPostMeta'));
  }

  public function registerCPT()
  {
    $default_args = [
      'supports'      => [
        'title', 'editor', 'thumbnail', 'excerpt'
      ],
      'public'        => true,
      'show_in_rest'  => true
    ];

    $args = wp_parse_args(
      $this->args(),
      $default_args
    );

    register_post_type(
      static::key(),
      $args
    );
  }

  public function registerPostMeta()
  {
    foreach (static::meta() ?? [] as $key => $args) {

      // Add Subtype
      $args = array_merge($args, ['object_subtype' => static::key()]);

      register_meta('post', $key, $args);
    }
  }
}
