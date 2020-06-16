<?php

namespace MUBase;


require_once 'Autoloader.php';
Autoloader::register();

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
  }
}


new Base;
