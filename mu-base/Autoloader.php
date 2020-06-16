<?php

namespace MUBase;

/**
 * Autoloads classes using PSR-4
 */

class Autoloader
{
  /**
   * Handles autoloading
   * 
   * @param string $class
   */
  public static function autoload($class)
  {
    if (0 !== strpos($class, __NAMESPACE__)) return;

    $class = substr($class, strlen(__NAMESPACE__));

    $file = dirname(__FILE__) . str_replace((array('\\', "\0")), array('/', ''), $class) . '.php';

    if (is_file($file)) require $file;
  }

  /**
   * Registers as an SPL autoloader
   * 
   * @param bool $prepend
   */
  public static function register($prepend = false)
  {
    spl_autoload_register(array(new self(), 'autoload'), true, $prepend);
  }
}
