<?php

namespace MUBase\Core\PostTypes;

abstract class AbstractCPT
{

  abstract public static function key(): string;

  abstract public function args(): array;


  public function init()
  {
    add_action('init', array($this, 'register'));
  }

  public function register()
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
}
