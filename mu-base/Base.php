<?php

namespace MUBase;

use MUBase\Core\DependencyInjection\Container;

class Base
{

  use Pluggable;

  const VERSION = '0.0.1';

  const DOMAIN = 'mu-base';

  protected $container;

  public function __construct()
  {
    $this->initContainer();

    $this->actions();
  }

  protected function initContainer()
  {

    $this->container = new Container([
      'mubase_domain'         => self::DOMAIN,
      'mubase_version'        => self::VERSION,
      'mubase_path'           => plugin_dir_path(__FILE__),
      'mubase_relative_path'  => basename(plugin_dir_path(__FILE__)),
      'mubase_url'            => plugin_dir_url(__FILE__),
      'mubase_style_handler'  => 'mubase-general-styles',
      'mubase_style_url'      => trailingslashit(plugin_dir_url(__FILE__)) . 'assets/inc/css/',
      'mubase_script_handler' => 'mubase-general-scripts',
      'mubase_script_url'     => trailingslashit(plugin_dir_url(__FILE__)) . 'assets/inc/js/',
      'mubase_acf_json_path'  => plugin_dir_path(__FILE__) . 'acf',
    ]);
  }

  public function load()
  {
    $this->container->registerServices([
      \MUBase\Core\Services\CacheService::class,
      \MUBase\Core\Services\ACFService::class,
    ]);
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

    return $this->container['mubase_version'];
  }
}
