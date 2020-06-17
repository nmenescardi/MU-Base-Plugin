<?php

namespace MUBase\Core\Taxonomies;

abstract class AbstractTaxonomy
{

  protected $custom_args;


  abstract static function key();

  public function init()
  {
    add_action('init', [$this, 'register']);
  }

  public function register()
  {
    $default_args = [
      'hierarchical' => true,
      'labels' => array(
        'name' => $this->plural_label,
        'singular_name' => $this->single_label,
        'search_items' => "Search $this->plural_label",
        'all_items' => "All $this->plural_label",
        'parent_item' => "Parent $this->single_label",
        'parent_item_colon' => "Parent $this->single_label:",
        'edit_item' => "Edit $this->single_label",
        'update_item' => "Update $this->single_label",
        'add_new_item' => "Add New $this->single_label",
        'new_item_name' => "New $this->single_label Name",
        'menu_name' => "$this->plural_label",
        'not_found' => "No $this->plural_label found."
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
