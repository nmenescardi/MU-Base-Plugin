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
      \MUBase\Core\Services\CPTService::class,
      \MUBase\Core\Services\CustomTaxonomyService::class,
      \MUBase\Core\Services\EnqueueScriptService::class,
    ]);
  }
}
