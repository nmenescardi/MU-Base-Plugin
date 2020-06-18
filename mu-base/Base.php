<?php

namespace MUBase;


class Base
{

  use Pluggable;

  public function __construct()
  {
    $this->constants();
    $this->filters();
    $this->actions();
  }

  public function constants()
  {
    if (!defined('MUBASE_PLUGIN_PATH'))
      define('MUBASE_PLUGIN_PATH', trailingslashit(plugin_dir_path(__FILE__)));

    if (!defined('MUBASE_PLUGIN_URL'))
      define('MUBASE_PLUGIN_URL', trailingslashit(plugin_dir_path(__FILE__)));

    if (!defined('MUBASE_PLUGIN_VERSION'))
      define('MUBASE_PLUGIN_VERSION', '0.0.1');

    if (!defined('MUBASE_STYLES_HANDLER'))
      define('MUBASE_STYLES_HANDLER', 'mubase-general-styles');

    if (!defined('MUBASE_STYLES_URL'))
      define('MUBASE_STYLES_URL', trailingslashit(plugin_dir_url(__FILE__)) . 'assets/inc/css/');

    if (!defined('MUBASE_SCRIPTS_HANDLER'))
      define('MUBASE_SCRIPTS_HANDLER', 'mubase-general-scripts');

    if (!defined('MUBASE_SCRIPTS_URL'))
      define('MUBASE_SCRIPTS_URL', trailingslashit(plugin_dir_url(__FILE__)) . 'assets/inc/js/');
  }

  public function filters()
  {

    // Saves ACF's settings under './acf' directory.
    add_filter(
      'acf/settings/save_json',
      function ($path) {
        $path = plugin_dir_path(__FILE__) . 'acf';
        return $path;
      }
    );

    // Automatically loads ACF's settings under './acf' directory.
    add_filter(
      'acf/settings/load_json',
      function ($path) {
        $path[] = plugin_dir_path(__FILE__) . 'acf';
        return $path;
      }
    );
  }

  public function actions()
  {
    add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));

    add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

    $this->init_custom_post_types([
      'MUBase\Core\PostTypes\CptExample'
    ]);

    $this->init_custom_taxonomy([
      'MUBase\Core\Taxonomies\TaxExample' => [
        \MUBase\Core\PostTypes\CptExample::key()
      ]
    ]);
  }

  protected function init_custom_post_types(array $cpt_classes)
  {
    foreach ($cpt_classes as $cpt_class) {
      (new $cpt_class)->init();
    }
  }

  protected function init_custom_taxonomy(array $taxonomies): void
  {
    foreach ($taxonomies as $tax_class => $associated_cpts) {

      $taxonomy = new $tax_class($associated_cpts);
      $taxonomy->init();
    }
  }

  public function enqueue_styles()
  {
    /* wp_enqueue_style(
      MUBASE_STYLES_HANDLER,
      MUBASE_STYLES_URL . 'main.min.css',
      array(),
      $this->get_cache_version_number()
    ); */
  }

  public function enqueue_scripts()
  {
    /* wp_enqueue_script(
      MUBASE_SCRIPTS_HANDLER,
      MUBASE_SCRIPTS_URL . 'main.min.js',
      array(),
      $this->get_cache_version_number(),
      true
    ); */
  }

  /**
   * Get Plugin Version Number if it is a PROD environment. Otherwise, just the timestamp to break the cache
   *
   * @return mixed
   */
  protected function get_cache_version_number()
  {

    if (WP_DEBUG === true)
      return microtime(true);

    return MUBASE_PLUGIN_VERSION;
  }
}


new Base;
