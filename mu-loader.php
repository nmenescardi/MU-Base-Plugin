<?php

/**
 * Load MU Plugins
 */

require_once 'mu-base/vendor/autoload.php';
require_once 'mu-base/Base.php';

add_action('plugins_loaded', array(new MUBase\Base(), 'load'));
