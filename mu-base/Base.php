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
  }

  public function filters()
  {
  }

  public function actions()
  {
  }
}


new Base;
