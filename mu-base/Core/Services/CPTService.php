<?php

namespace MUBase\Core\Services;

use Pimple\ServiceProviderInterface;

class CPTService implements ServiceProviderInterface
{

  public function register($container)
  {

    $this->init_custom_post_types([
      'MUBase\Core\PostTypes\CptExample'
    ]);
  }


  protected function init_custom_post_types(array $cpt_classes)
  {
    foreach ($cpt_classes as $cpt_class) {
      (new $cpt_class)->init();
    }
  }
}
