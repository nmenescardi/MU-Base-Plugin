<?php

namespace MUBase\Core\Taxonomies;

abstract class AbstractTaxonomy
{

  protected $custom_args;


  abstract static function key();

  abstract public function pluralLabel(): string;

  abstract public function singleLabel(): string;

  public function init()
  {
    add_action('init', [$this, 'register']);
  }

  public function register()
  {
    $default_args = [
      'hierarchical' => true,
      'labels' => array(
        'name' => $this->pluralLabel(),
        'singular_name' => $this->singleLabel(),
        'search_items' => "Search {$this->pluralLabel()}",
        'all_items' => "All {$this->pluralLabel()}",
        'parent_item' => "Parent {$this->singleLabel()}",
        'parent_item_colon' => "Parent {$this->singleLabel()}:",
        'edit_item' => "Edit {$this->singleLabel()}",
        'update_item' => "Update {$this->singleLabel()}",
        'add_new_item' => "Add New {$this->singleLabel()}",
        'new_item_name' => "New {$this->singleLabel()} Name",
        'menu_name' => "{$this->pluralLabel()}",
        'not_found' => "No {$this->pluralLabel()} found."
      ),
      'show_admin_column' => true,
      'show_in_rest' => true,
      'rewrite' => array(
        'slug' => $this->taxonomy_slug
      )
    ];

    $args = wp_parse_args($this->custom_args, $default_args);

    register_taxonomy(
      static::key(),
      $this->associated_cpts,
      $args
    );
  }
}
