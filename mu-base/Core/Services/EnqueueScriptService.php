<?php

namespace MUBase\Core\Services;

use Pimple\ServiceProviderInterface;

class EnqueueScriptService implements ServiceProviderInterface
{

  protected $container;

  public function register($container)
  {
    $this->container = $container;

    add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));

    add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
  }

  public function enqueue_styles()
  {
    wp_enqueue_style(
      $this->container['mubase_style_handler'],
      $this->container['mubase_style_url'] . 'main.min.css',
      array(),
      $this->cacheVersionNumber()
    );
  }

  public function enqueue_scripts()
  {
    wp_enqueue_script(
      $this->container['mubase_script_handler'],
      $this->container['mubase_script_url'] . 'main.min.js',
      array(),
      $this->cacheVersionNumber(),
      true
    );
  }

  /**
   * Get Plugin Version Number if it is a PROD environment. Otherwise, just the timestamp to break the cache
   *
   * @return mixed
   */
  protected function cacheVersionNumber()
  {

    if (WP_DEBUG === true)
      return microtime(true);

    return $this->container['mubase_version'];
  }
}
