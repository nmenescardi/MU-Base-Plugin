<?php

namespace MUBase\Core\Services;

use Pimple\ServiceProviderInterface;

class CustomTaxonomyService implements ServiceProviderInterface
{

  public function register($container)
  {

    $this->init_custom_taxonomy([
      'MUBase\Core\Taxonomies\TaxExample' => [
        \MUBase\Core\PostTypes\CptExample::key()
      ]
    ]);
  }


  protected function init_custom_taxonomy(array $taxonomies): void
  {
    foreach ($taxonomies as $tax_class => $associated_cpts) {

      $taxonomy = new $tax_class($associated_cpts);
      $taxonomy->init();
    }
  }
}
